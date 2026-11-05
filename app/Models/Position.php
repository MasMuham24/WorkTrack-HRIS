<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 *
 * @method static int count(string $columns = '*')
 * @method bool|null delete()
 */
class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'departement_id',
        'name',
        'position_type',
        'description',
    ];

    protected $casts = [
        'position_type' => 'string',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Relasi legacy ke departemen (1-ke-1 melalui positions.departement_id).
     */
    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'departement_id');
    }

    /**
     * Alias bahasa Inggris untuk relasi departemen legacy.
     */
    public function department(): BelongsTo
    {
        return $this->departement();
    }

    /**
     * Relasi ke departemens melalui tabel pivot departement_position.
     */
    public function departements(): BelongsToMany
    {
        return $this->belongsToMany(Departemen::class, 'departement_position', 'position_id', 'departement_id')
            ->withTimestamps();
    }

    /**
     * Alias bahasa Inggris untuk relasi departemens via pivot.
     */
    public function departments(): BelongsToMany
    {
        return $this->departements();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isGlobal(): bool
    {
        return $this->position_type === 'global';
    }

    public function isOfficeSpecific(): bool
    {
        return $this->position_type === 'office_specific';
    }
}
