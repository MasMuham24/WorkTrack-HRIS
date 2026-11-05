<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniquePositionName implements ValidationRule
{
    public function __construct(
        protected ?string $positionType,
        protected ?int $officeId = null,
        protected ?int $ignoreId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($this->positionType, ['global', 'office_specific'], true)) {
            return;
        }
        $query = DB::table('positions')->where('name', $value)->where('position_type', $this->positionType);
        if ($this->positionType === 'global') {
            $query->whereNull('office_id');
        } else {
            if ($this->officeId === null) {
                return;
            }
            $query->where('office_id', $this->officeId);
        }

        if ($this->ignoreId !== null) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('Nama posisi sudah digunakan dalam cakupan yang sama.');
        }
    }
}
