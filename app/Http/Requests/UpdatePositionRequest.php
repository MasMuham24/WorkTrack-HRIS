<?php

namespace App\Http\Requests;

use App\Rules\UniquePositionName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $position = $this->route('position');

        return [
            'name' => ['required', 'string', 'max:100', new UniquePositionName(
                $this->input('position_type'),
                $this->input('office_id') !== null ? (int) $this->input('office_id') : null,
                $position->id
            )],
            'position_type' => ['required', Rule::in(['global', 'office_specific'])],
            'office_id' => [Rule::requiredIf($this->input('position_type') === 'office_specific'), 'nullable', 'exists:offices,id'],
            'departement_ids' => ['required', 'array', 'min:1'],
            'departement_ids.*' => ['required', 'integer', 'distinct', 'exists:departemens,id'],
            'description' => ['nullable', 'string'],
        ];
    }
}
