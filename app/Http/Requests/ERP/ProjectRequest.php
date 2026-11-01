<?php

namespace App\Http\Requests\ERP;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->route('project')?->id ?? $this->route('project');
        $companyId = $this->input('company_id', 1);

        return [
            'company_id' => ['required', 'exists:erp_companies,id'],
            'branch_id' => ['nullable', 'exists:erp_branches,id'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('erp_projects', 'code')
                    ->where('company_id', $companyId)
                    ->ignore($projectId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['flat', 'plot', 'commercial', 'mixed'])],
            'status' => ['required', Rule::in(['planning', 'upcoming', 'ongoing', 'ready', 'completed', 'on_hold'])],
            'address' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'land_area' => ['nullable', 'string', 'max:100'],
            'land_area_unit' => ['nullable', 'string', 'max:32'],
            'ownership_type' => ['required', Rule::in(['own_land', 'joint_venture', 'developer_share'])],
            'launch_date' => ['nullable', 'date'],
            'target_end_date' => ['nullable', 'date', 'after_or_equal:launch_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

