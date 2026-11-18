<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 *
 * @method static int count(string $columns = '*')
 * @method static Builder whereDate(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static Builder where(string|array|\Closure $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method bool|null delete()
 */
class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'approved_by',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
