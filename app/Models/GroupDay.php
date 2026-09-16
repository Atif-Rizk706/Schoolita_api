<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
