<?php

namespace App\Http\Controllers;

use App\Models\DailyEntry;
use Illuminate\Http\Request;

class HasilKlasifikasiController extends Controller
{
    public function index()
    {
        $entry = DailyEntry::orderBy('created_at', 'desc')->first();

        // Jika ingin tampilkan akurasi dan confusion matrix dari file JSON hasil training
        $accuracy = null;
        $confMatrix = null;
        $jsonPath = base_path('app/Python/training_result.json');
        if (file_exists($jsonPath)) {
            $result = json_decode(file_get_contents($jsonPath), true);
            $accuracy = $result['accuracy'] ?? null;
            $confMatrix = $result['confusion_matrix'] ?? null;
        }

        return view('HasilKlasifikasi.hasil_klasifikasi', compact('entry', 'accuracy', 'confMatrix'));
    }
}
