<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $dn = Carbon::now()->format('d-m-Y');

        return view('analytics', compact('companies', 'dn'));
    }

    /**
     * Returns the SQL expression and alias for grouping by period.
     */
    private function periodGroupExpression(string $period): array
    {
        switch ($period) {
            case 'quarterly':
                return [
                    DB::raw("CONCAT(YEAR(date_accident), ' Q', QUARTER(date_accident)) as period_label"),
                    DB::raw("CONCAT(YEAR(date_accident), QUARTER(date_accident))"),
                ];
            case 'halfyearly':
                return [
                    DB::raw("CONCAT(YEAR(date_accident), ' H', IF(MONTH(date_accident) <= 6, 1, 2)) as period_label"),
                    DB::raw("CONCAT(YEAR(date_accident), IF(MONTH(date_accident) <= 6, 1, 2))"),
                ];
            case 'yearly':
                return [
                    DB::raw('YEAR(date_accident) as period_label'),
                    DB::raw('YEAR(date_accident)'),
                ];
            default: // monthly
                return [
                    DB::raw("DATE_FORMAT(date_accident, '%Y-%m') as period_label"),
                    DB::raw("DATE_FORMAT(date_accident, '%Y-%m')"),
                ];
        }
    }

    private function buildStats(int $companyId, string $from, string $to, string $period): array
    {
        [$selectExpr, $groupExpr] = $this->periodGroupExpression($period);

        // --- 1. Damage costs per period ---
        $damage_costs = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->select($selectExpr, DB::raw('SUM(damage_costs) as damage_costs, COUNT(*) as claim_count'))
            ->groupBy($groupExpr)
            ->orderBy($groupExpr)
            ->get();

        // --- 2. Savings per period ---
        $saved_costs = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->select($selectExpr, DB::raw('SUM(COALESCE(recovery_costs,0) + COALESCE(replacement_vehicle_costs,0) + COALESCE(expert_costs,0) + COALESCE(other_costs,0) + COALESCE(deductible_excess_costs,0) + COALESCE(insurance_costs,0) + COALESCE(invoice_amount,0)) as saved_costs'))
            ->groupBy($groupExpr)
            ->orderBy($groupExpr)
            ->get();

        // --- 3. Damage per kind (transport / traffic / other) ---
        $kind_stats = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->select(DB::raw('damage_kind, COUNT(*) as claim_count, SUM(COALESCE(damage_costs,0)) as damage_costs'))
            ->groupBy('damage_kind')
            ->get()
            ->keyBy('damage_kind');

        $kind_labels_map = ['transport' => 'Transportschade', 'traffic' => 'Verkeersschade', 'other' => 'Overig'];
        $kind_data = [];
        foreach ($kind_labels_map as $key => $label) {
            $kind_data[] = [
                'label'       => $label,
                'claim_count' => $kind_stats->get($key)->claim_count ?? 0,
                'damage_costs' => round((float)($kind_stats->get($key)->damage_costs ?? 0), 2),
            ];
        }

        // --- 4. Activity breakdown (transport claims only, from damage_origin JSON) ---
        $transportClaims = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->where('damage_kind', 'transport')
            ->whereNotNull('damage_origin')
            ->select('damage_origin', 'damage_costs')
            ->get();

        $activityTotals = [];
        $damageOriginLabels = Claim::DAMAGE_ORIGIN;
        foreach ($transportClaims as $claim) {
            $origins = json_decode($claim->damage_origin, true);
            if (!is_array($origins)) {
                continue;
            }
            foreach ($origins as $originKey) {
                $label = $damageOriginLabels[$originKey] ?? $originKey;
                if (!isset($activityTotals[$label])) {
                    $activityTotals[$label] = ['label' => $label, 'claim_count' => 0, 'damage_costs' => 0];
                }
                $activityTotals[$label]['claim_count']++;
                $activityTotals[$label]['damage_costs'] += (float)$claim->damage_costs;
            }
        }
        arsort($activityTotals);
        $activity_data = array_values(array_slice($activityTotals, 0, 15));
        foreach ($activity_data as &$a) {
            $a['damage_costs'] = round($a['damage_costs'], 2);
        }
        unset($a);

        // --- 5. Per driver (driver_vehicle → drivers → contacts) ---
        $driver_stats = DB::table('claims')
            ->join('drivers', 'claims.driver_vehicle', '=', 'drivers.id')
            ->join('contacts', 'drivers.contact_id', '=', 'contacts.id')
            ->where('claims.company_id', $companyId)
            ->whereBetween('claims.date_accident', [$from, $to])
            ->whereNull('claims.deleted_at')
            ->whereNotNull('claims.driver_vehicle')
            ->select(
                DB::raw("CONCAT(contacts.first_name, ' ', contacts.last_name) as driver_name"),
                DB::raw('COUNT(*) as claim_count'),
                DB::raw('SUM(COALESCE(claims.damage_costs,0)) as damage_costs')
            )
            ->groupBy('claims.driver_vehicle', 'contacts.first_name', 'contacts.last_name')
            ->orderByDesc('damage_costs')
            ->limit(15)
            ->get();

        // --- 6. Per vehicle (vehicle_id → vehicles) ---
        $vehicle_stats = DB::table('claims')
            ->join('vehicles', 'claims.vehicle_id', '=', 'vehicles.id')
            ->where('claims.company_id', $companyId)
            ->whereBetween('claims.date_accident', [$from, $to])
            ->whereNull('claims.deleted_at')
            ->whereNotNull('claims.vehicle_id')
            ->select(
                DB::raw("CONCAT(COALESCE(vehicles.brand,''), ' ', vehicles.plates) as vehicle_name"),
                DB::raw('COUNT(*) as claim_count'),
                DB::raw('SUM(COALESCE(claims.damage_costs,0)) as damage_costs')
            )
            ->groupBy('claims.vehicle_id', 'vehicles.brand', 'vehicles.plates')
            ->orderByDesc('damage_costs')
            ->limit(15)
            ->get();

        // --- 7. Claims: total / toegewezen / afgewezen ---
        $total_claims     = DB::table('claims')->where('company_id', $companyId)->whereBetween('date_accident', [$from, $to])->whereNull('deleted_at')->count();
        $assigned_claims  = DB::table('claims')->where('company_id', $companyId)->whereBetween('date_accident', [$from, $to])->whereNull('deleted_at')->whereNotNull('assignee_id')->count();
        $denied_claims    = DB::table('claims')->where('company_id', $companyId)->whereBetween('date_accident', [$from, $to])->whereNull('deleted_at')->where('status', 'claim_denied')->count();

        $status_data = [
            ['label' => 'Totaal',      'count' => $total_claims],
            ['label' => 'Toegewezen',  'count' => $assigned_claims],
            ['label' => 'Afgewezen',   'count' => $denied_claims],
        ];

        // --- 8. Verhaalbaar / niet-verhaalbaar ---
        $recoverable_stats = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->select(DB::raw('recoverable_claim, COUNT(*) as claim_count'))
            ->groupBy('recoverable_claim')
            ->get()
            ->keyBy('recoverable_claim');

        $recoverable_labels = [
            'yes'       => 'Verhaalbaar',
            'partially' => 'Gedeeltelijk verhaalbaar',
            'no'        => 'Niet verhaalbaar',
            'unknown'   => 'Onbekend',
        ];
        $recoverable_data = [];
        foreach ($recoverable_labels as $key => $label) {
            $recoverable_data[] = [
                'label' => $label,
                'count' => $recoverable_stats->get($key)->claim_count ?? 0,
            ];
        }

        // --- 9. Verwijtbaarheid (at-fault) ---
        $verwijtbaar_stats = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->whereNotNull('verwijtbaar')
            ->select(DB::raw('verwijtbaar, COUNT(*) as claim_count'))
            ->groupBy('verwijtbaar')
            ->get()
            ->keyBy('verwijtbaar');

        $verwijtbaar_data = [
            ['label' => 'Verwijtbaar',      'count' => $verwijtbaar_stats->get('yes')->claim_count ?? 0],
            ['label' => 'Niet verwijtbaar', 'count' => $verwijtbaar_stats->get('no')->claim_count ?? 0],
        ];

        // --- 10. Letselclaims (injury) ---
        $injury_stats = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->select(DB::raw('injury, COUNT(*) as claim_count'))
            ->groupBy('injury')
            ->get()
            ->keyBy('injury');

        $injury_labels_map = ['yes' => 'Letsel', 'no' => 'Geen letsel', 'other' => 'Anders'];
        $injury_data = [];
        foreach ($injury_labels_map as $key => $label) {
            $injury_data[] = ['label' => $label, 'count' => $injury_stats->get($key)->claim_count ?? 0];
        }

        // --- 11. Tegenpartij type ---
        $opposite_stats = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->whereNotNull('opposite_type')
            ->select(DB::raw('opposite_type, COUNT(*) as claim_count'))
            ->groupBy('opposite_type')
            ->get()
            ->keyBy('opposite_type');

        $opposite_labels_map = [
            'private'  => 'Particulier',
            'business' => 'Zakelijk',
            'lease_car' => 'Leaseauto',
            'unknown'  => 'Onbekend',
            'obstacle' => 'Obstakel',
        ];
        $opposite_data = [];
        foreach ($opposite_labels_map as $key => $label) {
            $count = $opposite_stats->get($key)->claim_count ?? 0;
            $opposite_data[] = ['label' => $label, 'count' => $count];
        }

        // --- 12. Kostensamenstelling (breakdown of savings components) ---
        $cost_row = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->select(DB::raw(
                'SUM(COALESCE(recovery_costs,0)) as recovery_costs,'
                . 'SUM(COALESCE(replacement_vehicle_costs,0)) as replacement_vehicle_costs,'
                . 'SUM(COALESCE(expert_costs,0)) as expert_costs,'
                . 'SUM(COALESCE(other_costs,0)) as other_costs,'
                . 'SUM(COALESCE(deductible_excess_costs,0)) as deductible_excess_costs,'
                . 'SUM(COALESCE(insurance_costs,0)) as insurance_costs,'
                . 'SUM(COALESCE(invoice_amount,0)) as invoice_amount'
            ))
            ->first();

        $cost_breakdown_data = [
            ['label' => 'Berging/herstel',  'amount' => round((float)($cost_row->recovery_costs ?? 0), 2)],
            ['label' => 'Vervangende auto', 'amount' => round((float)($cost_row->replacement_vehicle_costs ?? 0), 2)],
            ['label' => 'Expert kosten',    'amount' => round((float)($cost_row->expert_costs ?? 0), 2)],
            ['label' => 'Overige kosten',   'amount' => round((float)($cost_row->other_costs ?? 0), 2)],
            ['label' => 'Eigen risico',     'amount' => round((float)($cost_row->deductible_excess_costs ?? 0), 2)],
            ['label' => 'Verzekering',      'amount' => round((float)($cost_row->insurance_costs ?? 0), 2)],
            ['label' => 'ASP vergoeding',   'amount' => round((float)($cost_row->invoice_amount ?? 0), 2)],
        ];

        // --- 13. Gemiddelde afhandelingstijd per periode ---
        $avg_resolution = DB::table('claims')
            ->where('company_id', $companyId)
            ->whereBetween('date_accident', [$from, $to])
            ->whereNull('deleted_at')
            ->whereNotNull('closed_at')
            ->select($selectExpr, DB::raw('ROUND(AVG(DATEDIFF(closed_at, date_accident)),1) as avg_days, COUNT(*) as claim_count'))
            ->groupBy($groupExpr)
            ->orderBy($groupExpr)
            ->get();

        return compact(
            'damage_costs',
            'saved_costs',
            'kind_data',
            'activity_data',
            'driver_stats',
            'vehicle_stats',
            'status_data',
            'recoverable_data',
            'verwijtbaar_data',
            'injury_data',
            'opposite_data',
            'cost_breakdown_data',
            'avg_resolution'
        );
    }

    public function getData(Request $request)
    {
        $request->validate([
            'company'   => 'required|integer|exists:companies,id',
            'startdate' => 'required|string',
            'enddate'   => 'required|string',
            'period'    => 'nullable|in:monthly,quarterly,halfyearly,yearly',
        ]);

        $from   = Carbon::parse($request->startdate)->format('Y-m-d');
        $to     = Carbon::parse($request->enddate)->format('Y-m-d');
        $period = $request->input('period', 'monthly');

        $stats = $this->buildStats((int)$request->company, $from, $to, $period);

        return response()->json($stats, 200);
    }

    public function export(Request $request)
    {
        $request->validate([
            'company'   => 'required|integer|exists:companies,id',
            'startdate' => 'required|string',
            'enddate'   => 'required|string',
            'period'    => 'nullable|in:monthly,quarterly,halfyearly,yearly',
        ]);

        $from    = Carbon::parse($request->startdate)->format('Y-m-d');
        $to      = Carbon::parse($request->enddate)->format('Y-m-d');
        $period  = $request->input('period', 'monthly');
        $company = Company::findOrFail((int)$request->company);
        $stats   = $this->buildStats($company->id, $from, $to, $period);

        $filename = 'statistieken-' . str_replace(' ', '-', strtolower($company->name)) . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($stats) {
            $fp = fopen('php://output', 'w');

            // 1. Schadebedrag per periode
            fputcsv($fp, ['Schadebedrag per periode']);
            fputcsv($fp, ['Periode', 'Aantal claims', 'Schadebedrag (€)']);
            foreach ($stats['damage_costs'] as $row) {
                fputcsv($fp, [$row->period_label, $row->claim_count, $row->damage_costs]);
            }
            fputcsv($fp, []);

            // 2. Besparing per periode
            fputcsv($fp, ['Besparing per periode']);
            fputcsv($fp, ['Periode', 'Besparing (€)']);
            foreach ($stats['saved_costs'] as $row) {
                fputcsv($fp, [$row->period_label, $row->saved_costs]);
            }
            fputcsv($fp, []);

            // 3. Schadebedrag per soort
            fputcsv($fp, ['Schadebedrag per soort']);
            fputcsv($fp, ['Soort', 'Aantal claims', 'Schadebedrag (€)']);
            foreach ($stats['kind_data'] as $row) {
                fputcsv($fp, [$row['label'], $row['claim_count'], $row['damage_costs']]);
            }
            fputcsv($fp, []);

            // 4. Schadebedrag per activiteit
            fputcsv($fp, ['Schadebedrag per activiteit (transportschade)']);
            fputcsv($fp, ['Activiteit', 'Aantal claims', 'Schadebedrag (€)']);
            foreach ($stats['activity_data'] as $row) {
                fputcsv($fp, [$row['label'], $row['claim_count'], $row['damage_costs']]);
            }
            fputcsv($fp, []);

            // 5. Per medewerker
            fputcsv($fp, ['Schade per medewerker']);
            fputcsv($fp, ['Medewerker', 'Aantal claims', 'Schadebedrag (€)']);
            foreach ($stats['driver_stats'] as $row) {
                fputcsv($fp, [$row->driver_name, $row->claim_count, $row->damage_costs]);
            }
            fputcsv($fp, []);

            // 6. Per voertuig
            fputcsv($fp, ['Schade per voertuig']);
            fputcsv($fp, ['Voertuig', 'Aantal claims', 'Schadebedrag (€)']);
            foreach ($stats['vehicle_stats'] as $row) {
                fputcsv($fp, [$row->vehicle_name, $row->claim_count, $row->damage_costs]);
            }
            fputcsv($fp, []);

            // 7. Claims status
            fputcsv($fp, ['Aantal claims per status']);
            fputcsv($fp, ['Status', 'Aantal']);
            foreach ($stats['status_data'] as $row) {
                fputcsv($fp, [$row['label'], $row['count']]);
            }
            fputcsv($fp, []);

            // 8. Verhaalbaarheid
            fputcsv($fp, ['Verhaalbare vs niet-verhaalbare schade']);
            fputcsv($fp, ['Categorie', 'Aantal']);
            foreach ($stats['recoverable_data'] as $row) {
                fputcsv($fp, [$row['label'], $row['count']]);
            }
            fputcsv($fp, []);

            // 9. Verwijtbaarheid
            fputcsv($fp, ['Verwijtbaarheid']);
            fputcsv($fp, ['Categorie', 'Aantal']);
            foreach ($stats['verwijtbaar_data'] as $row) {
                fputcsv($fp, [$row['label'], $row['count']]);
            }
            fputcsv($fp, []);

            // 10. Letselclaims
            fputcsv($fp, ['Letselclaims']);
            fputcsv($fp, ['Categorie', 'Aantal']);
            foreach ($stats['injury_data'] as $row) {
                fputcsv($fp, [$row['label'], $row['count']]);
            }
            fputcsv($fp, []);

            // 11. Tegenpartij type
            fputcsv($fp, ['Tegenpartij type']);
            fputcsv($fp, ['Type', 'Aantal']);
            foreach ($stats['opposite_data'] as $row) {
                fputcsv($fp, [$row['label'], $row['count']]);
            }
            fputcsv($fp, []);

            // 12. Kostensamenstelling
            fputcsv($fp, ['Kostensamenstelling besparing']);
            fputcsv($fp, ['Kostenpost', 'Bedrag (€)']);
            foreach ($stats['cost_breakdown_data'] as $row) {
                fputcsv($fp, [$row['label'], $row['amount']]);
            }
            fputcsv($fp, []);

            // 13. Gemiddelde afhandelingstijd
            fputcsv($fp, ['Gemiddelde afhandelingstijd']);
            fputcsv($fp, ['Periode', 'Gem. dagen', 'Afgesloten claims']);
            foreach ($stats['avg_resolution'] as $row) {
                fputcsv($fp, [$row->period_label, $row->avg_days, $row->claim_count]);
            }

            fclose($fp);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function invoices()
    {
        $claims = Claim::where('invoice_settlement_asp', 0)->orWhere('invoice_settlement_asp', NULL)->get();
        $companies = Company::get();

        return view('invoices', compact('claims', 'companies'));
    }
}
