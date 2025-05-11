<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HourlyEntry;
use App\Models\DailyEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class HourlyController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal dari request (misal dari session flash atau query string)
        $date = $request->input('date') ?? session('last_date');

        $hourlyEntries = [];
        if ($date) {
            $hourlyEntries = HourlyEntry::where('date', $date)->get();
        }
      

        return view('DataHourly.data_minyak', [
            'hourlyEntries' => $hourlyEntries,
            'selectedDate' => $date,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'data' => 'required|array|min:1',
            'data.*.jam' => 'required',
            'data.*.alb' => 'required|numeric',
            'data.*.air' => 'required|numeric',
            'data.*.kotoran' => 'required|numeric',
        ]);

        $date = $request->input('date');

        // 1. Hitung rata-rata
        $avgAlb = collect($request->data)->avg('alb');
        $avgAir = collect($request->data)->avg('air');
        $avgKotoran = collect($request->data)->avg('kotoran');

        // 2. Simpan data ke daily_entries tanpa label dulu
        $daily = DailyEntry::create([
            'date' => $date,
            'avg_alb' => $avgAlb,
            'avg_air' => $avgAir,
            'avg_kotoran' => $avgKotoran,
            'label' => null, // akan diisi setelah prediksi
        ]);

        // 3. Simpan data per jam ke hourly_entries
        foreach ($request->data as $row) {
            HourlyEntry::create([
                'date' => $date,
                'jam' => $row['jam'],
                'alb' => $row['alb'],
                'air' => $row['air'],
                'kotoran' => $row['kotoran'],
                'daily_entry_id' => $daily->id,
            ]);
        }

        // 4. Panggil Python script untuk prediksi label
        $python = 'C:\Users\dhyna\AppData\Local\Programs\Python\Python313\python.exe';
        $script = base_path('app/Python/predictKnn.py');
        $command = "\"{$python}\" \"{$script}\" {$avgAlb} {$avgAir} {$avgKotoran}";
        exec($command, $output, $status);
        Log::info('KNN Output:', $output);
        Log::info('KNN Status: ' . $status);

        // Misal output[0] adalah label hasil prediksi
        if ($status === 0 && !empty($output[0])) {
            $daily->label = $output[0];
            $daily->save();
        }

        return redirect()->route('hourly.index', ['date' => $date])
            ->with('success', 'Data berhasil disimpan dan diklasifikasikan!');
    }
}
