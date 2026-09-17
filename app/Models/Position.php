<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static int count(string $columns = '*')
 */
class Position extends Model
{
    use HasFactory;
    protected $fillable = [
        'departement_id',
        'name',
        'description',
    ];

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'departement_id');
    }

    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }
}
