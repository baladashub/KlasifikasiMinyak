<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyEntry extends Model
{
    protected $table = 'daily_entries';
    
    protected $fillable = [
        'date',
        'avg_alb',
        'avg_air',
        'avg_kotoran',
        'label',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'avg_alb' => 'double',
        'avg_air' => 'double',
        'avg_kotoran' => 'double'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
