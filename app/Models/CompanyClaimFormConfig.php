<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyClaimFormConfig extends Model
{
    public $table = 'company_claim_form_configs';

    protected $fillable = [
        'company_id',
        'field_name',
        'is_enabled',
        'is_required',
        'include_in_notification',
        'notification_label',
        'conditional_logic',
        'display_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_required' => 'boolean',
        'include_in_notification' => 'boolean',
        'conditional_logic' => 'array',
        'display_order' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all available claim fields that can be configured
     */
    public static function getAvailableFields(): array
    {
        return [
            'form_type' => 'Type formulier',
            'complaint_description' => 'Klacht omschrijving',
            'subject' => 'Onderwerp',
            'date_accident' => 'Datum ongeval',
            'injury' => 'Letselschade',
            'injury_other' => 'Letselschade anders',
            'injury_office_id' => 'Letselschade kantoor',
            'damage_kind' => 'Soort schade',
            'recoverable_claim' => 'Verhaalbare schade',
            'vehicle_plates' => 'Kenteken voertuig',
            'driver_vehicle' => 'Bestuurder voertuig',
            'damaged_part' => 'Beschadigd onderdeel',
            'damaged_area' => 'Beschadigd gebied',
            'damage_origin' => 'Schadeoorzaak',
            'opposite_type' => 'Type wederpartij',
            'obstacle' => 'Obstakel',
            'vehicle_plates_opposite' => 'Kenteken wederpartij',
            'driver_vehicle_opposite' => 'Bestuurder wederpartij',
            'damaged_part_opposite' => 'Beschadigd onderdeel wederpartij',
            'damaged_area_opposite' => 'Beschadigd gebied wederpartij',
            'damage_origin_opposite' => 'Schadeoorzaak wederpartij',
            'op_name' => 'Naam wederpartij',
            'op_street' => 'Straat wederpartij',
            'op_zipcode' => 'Postcode wederpartij',
            'op_city' => 'Plaats wederpartij',
            'op_country' => 'Land wederpartij',
            'op_phone' => 'Telefoon wederpartij',
            'op_email' => 'Email wederpartij',
            'loading_photos' => 'Foto\'s laden',
            'unloading_photos' => 'Foto\'s lossen',
            'waybill_signed_at_loading' => 'Vrachtbrief getekend laden',
            'waybill_signed_at_unloading' => 'Vrachtbrief getekend lossen',
            'opposite_claim_no' => 'Schade nummer wederpartij',
            'damage_files' => 'Schade bestanden',
            'report_files' => 'Rapport bestanden',
            'financial_files' => 'Financiële bestanden',
            'other_files' => 'Overige bestanden',
        ];
    }

    /**
     * Evaluate conditional logic against form data
     */
    public function evaluateCondition(array $formData): bool
    {
        if (empty($this->conditional_logic)) {
            return true;
        }

        return $this->evaluateLogicNode($this->conditional_logic, $formData);
    }

    /**
     * Recursively evaluate a logic node (AND/OR)
     */
    private function evaluateLogicNode(array $node, array $formData): bool
    {
        $operator = $node['operator'] ?? 'AND';
        $conditions = $node['conditions'] ?? [];

        if (empty($conditions)) {
            return true;
        }

        $results = [];
        foreach ($conditions as $condition) {
            // If condition has nested conditions, recurse
            if (isset($condition['operator'])) {
                $results[] = $this->evaluateLogicNode($condition, $formData);
            } else {
                // Evaluate simple condition
                $results[] = $this->evaluateSimpleCondition($condition, $formData);
            }
        }

        // Apply AND/OR logic
        if ($operator === 'OR') {
            return in_array(true, $results, true);
        } else {
            return !in_array(false, $results, true);
        }
    }

    /**
     * Evaluate a simple field condition
     */
    private function evaluateSimpleCondition(array $condition, array $formData): bool
    {
        $field = $condition['field'] ?? '';
        $operator = $condition['operator'] ?? 'equals';
        $value = $condition['value'] ?? '';

        $fieldValue = $formData[$field] ?? null;

        switch ($operator) {
            case 'equals':
                return $fieldValue == $value;
            case 'not_equals':
                return $fieldValue != $value;
            case 'contains':
                return is_array($fieldValue) && in_array($value, $fieldValue);
            case 'not_contains':
                return !is_array($fieldValue) || !in_array($value, $fieldValue);
            case 'empty':
                return empty($fieldValue);
            case 'not_empty':
                return !empty($fieldValue);
            default:
                return false;
        }
    }
}
