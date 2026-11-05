<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidPositionDepartement implements ValidationRule
{
    public function __construct(private string $positionType, private ?int $officeId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            return;
        }
        if ($this->positionType === 'global') {
            return;
        }
        if ($this->positionType !== 'office_specific' || $this->officeId === null) {
            $fail('Posisi office-specific wajib memiliki kantor yang valid');

            return;
        }
        $validDepartements = DB::table('departemens')->whereIn('id', $value)->where('office_id', $this->officeId)->count();
        if ($validDepartements !== count($value)) {
            $fail('Semua departemen untuk posisi office-specific harus berasal dari kantor yang sama.');
        }
    }
}
