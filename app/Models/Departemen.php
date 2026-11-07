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
 * @method static \Illuminate\Database\Eloquent\Builder where(string|array|\Closure $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method bool|null delete()
 */
class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemens';

    protected $fillable = [
        'office_id',
        'name',
        'description',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Relasi legacy ke positions (melalui positions.departement_id).
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'departement_id');
    }

    /**
     * Relasi ke positions melalui tabel pivot departement_position.
     */
    public function positionsViaPivot(): BelongsToMany
    {
        return $this->belongsToMany(
            Position::class,
            'departement_position',
            'departement_id',
            'position_id'
        )->withTimestamps();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'departement_id');
    }
}
