<?php

namespace App\Http\Requests\Department;

use App\Models\Departemen;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $departemen = $this->route('departemen');
        $departemenId = $departemen instanceof Departemen ? $departemen->id : $departemen;
        $officeId = $departemen instanceof Departemen
            ? $departemen->office_id
            : Departemen::where('id', $departemenId)->value('office_id');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('departemens', 'name')
                    ->where(function ($query) use ($officeId) {
                        return $query->where('office_id', $officeId);
                    })
                    ->ignore($departemenId),
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
            'name.required' => 'Nama departemen wajib diisi.',
            'name.unique' => 'Nama departemen sudah digunakan pada kantor ini.',
            'name.max' => 'Nama departemen maksimal 100 karakter.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
        ];
    }
}
