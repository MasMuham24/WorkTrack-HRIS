<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method bool|null delete()
 */
class Office extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius',
        'check_in_time',
        'check_out_time',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius' => 'integer',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
