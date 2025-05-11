<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HourlyEntry extends Model
{
    protected $fillable = ['date', 'jam', 'alb', 'air', 'kotoran'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dailyEntry()
    {
        return $this->belongsTo(DailyEntry::class, 'daily_entry_id');
    }
}
