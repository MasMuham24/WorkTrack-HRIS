<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'office_id' => [
                'required',
                'exists:offices,id',
            ],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('departemens', 'name')->where(function ($query) {
                    return $query->where('office_id', $this->office_id);
                }),
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'office_id.required' => 'Kantor wajib dipilih.',
            'office_id.exists' => 'Kantor yang dipilih tidak valid.',
            'name.required' => 'Nama departemen wajib diisi.',
            'name.unique' => 'Nama departemen sudah digunakan pada kantor ini.',
            'name.max' => 'Nama departemen maksimal 100 karakter.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
        ];
    }
}
