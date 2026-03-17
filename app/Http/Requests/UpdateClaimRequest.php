<?php

namespace App\Http\Requests;

use App\Models\Claim;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateClaimRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('claim_edit');
    }

    public function rules()
    {
        return [
            'company_id' => [
                'required',
                'integer',
            ],
            'insurance_company_id' => [
                'nullable',
                'integer',
            ],
            'subject' => [
                'string',
                'required',
            ],
            'claim_number' => [
                'string',
                'required',
            ],
            'date_accident' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'injury_other' => [
                'string',
                'nullable',
            ],
            'vehicle_id' => [
                'required',
                'integer',
            ],
            'requested_at' => [
                'nullable',
                'date_format:' . config('panel.date_format'),
            ],
            'report_received_at' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'herstel_op' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'injury_requested_at' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'damage_files' => [
                'array',
                'max:10',
            ],
            'damage_files.*' => [
                'nullable',
                'string',
            ],
            'report_files' => [
                'array',
                'max:10',
            ],
            'report_files.*' => [
                'nullable',
                'string',
            ],
            'financial_files' => [
                'array',
                'max:10',
            ],
            'financial_files.*' => [
                'nullable',
                'string',
            ],
            'other_files' => [
                'array',
                'max:10',
            ],
            'other_files.*' => [
                'nullable',
                'string',
            ],
            'bevestiging_kl_at'      => ['nullable', 'date_format:' . config('panel.date_format')],
            'saf_binnen_at'          => ['nullable', 'date_format:' . config('panel.date_format')],
            'info_chf_at'            => ['nullable', 'date_format:' . config('panel.date_format')],
            'info_kl_wp_at'          => ['nullable', 'date_format:' . config('panel.date_format')],
            'beoordeling_at'         => ['nullable', 'date_format:' . config('panel.date_format')],
            'schadebedrag_bekend_at' => ['nullable', 'date_format:' . config('panel.date_format')],
            'naar_vzk_at'            => ['nullable', 'date_format:' . config('panel.date_format')],
            'naar_shb_gge_at'        => ['nullable', 'date_format:' . config('panel.date_format')],
            'goedkeuring_og_at'      => ['nullable', 'date_format:' . config('panel.date_format')],
            'factuur_ontvangen_at'   => ['nullable', 'date_format:' . config('panel.date_format')],
            'factuur_adm_at'         => ['nullable', 'date_format:' . config('panel.date_format')],
            'brief_chf_at'           => ['nullable', 'date_format:' . config('panel.date_format')],
            'dossier_controle_at'    => ['nullable', 'date_format:' . config('panel.date_format')],
            'dossier_heropend_at'    => ['nullable', 'date_format:' . config('panel.date_format')],
            'dossier_nvt'            => ['nullable', 'array'],
            'dossier_nvt.*'          => ['string'],
        ];
    }
}
