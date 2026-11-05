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

    public function departements(): HasMany
    {
        return $this->hasMany(Departemen::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
