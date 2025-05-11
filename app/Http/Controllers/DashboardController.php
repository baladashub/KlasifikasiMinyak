<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataTraining;
use App\Models\DataMinyak;
use App\Models\HourlyEntry;
use App\Models\DailyEntry;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalHourly = HourlyEntry::count();
        $totalDaily = DailyEntry::count();

        // Hitung distribusi label
        $labelCounts = DailyEntry::select('label', DB::raw('count(*) as total'))
            ->groupBy('label')
            ->pluck('total', 'label')
            ->toArray();

        return view('dashboard', compact('totalHourly', 'totalDaily', 'labelCounts'));
    }
} 