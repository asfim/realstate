<?php

namespace App\Http\Requests\ERP;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $unitId = $this->route('unit')?->id ?? $this->route('unit');
        $projectId = $this->input('project_id', $this->route('unit')?->project_id);

        return [
            'company_id' => ['required', 'exists:erp_companies,id'],
            'project_id' => ['required', 'exists:erp_projects,id'],
            'building_id' => ['nullable', 'exists:erp_buildings,id'],
            'floor_id' => ['nullable', 'exists:erp_floors,id'],
            'block_id' => ['nullable', 'exists:erp_blocks,id'],
            'road_id' => ['nullable', 'exists:erp_roads,id'],
            'unit_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('erp_units', 'unit_code')
                    ->where('project_id', $projectId)
                    ->ignore($unitId),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['flat', 'plot', 'shop', 'office', 'commercial_other'])],
            'status' => ['required', Rule::in(['draft', 'available', 'held', 'booked', 'sold', 'registration_pending', 'registered', 'handed_over', 'cancelled'])],
            'facing' => ['nullable', Rule::in(['north', 'south', 'east', 'west', 'north_east', 'south_east', 'north_west', 'south_west'])],
            'area' => ['required', 'numeric', 'gt:0'],
            'area_unit' => ['required', Rule::in(['sqft', 'sqm', 'katha', 'decimal'])],
            'asking_price' => ['required', 'numeric', 'min:0'],
            'min_price' => ['nullable', 'numeric', 'min:0', 'lte:asking_price'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'balconies' => ['nullable', 'integer', 'min:0'],
            'parking_included' => ['nullable', 'boolean'],
            'road_width_ft' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

