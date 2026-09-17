<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static int count(string $columns = '*')
 */
class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemens';

    protected $fillable = [
        'name',
        'description',
    ];

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'departement_id');
    }

    public function users():HasMany
    {
        return $this->hasMany(User::class, 'departement_id');
    }
}
