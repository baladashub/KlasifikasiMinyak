<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyEntry;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class ImportController extends Controller
{
    public function index(Request $request)
    {
        $totalData = DailyEntry::count();
    $query = DailyEntry::orderBy('date', 'desc');

    if ($request->filled('search_date')) {
        $query->whereDate('date', $request->search_date);
    }

    $entries = $query->paginate(10);
    return view('import.index', compact('totalData', 'entries'));
    }

    public function autolabel()
    {
        Log::info('Fungsi autolabel dipanggil');
        dd('MASUK AUTOLABEL');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            // Simpan file upload ke storage
            $file = $request->file('file');
            $filename = 'uploaded_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('CleanData', $filename);
            $fullPath = Storage::path($path);

            Log::info('File uploaded', ['path' => $path]);

            // Cek apakah file sudah clean
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $headers = $sheet->rangeToArray('A1:F1')[0];

            $expected = ['Tanggal', 'Hari', 'Jam', 'ALB', 'Air', 'Kotoran'];
            $isClean = true;
            for ($i = 0; $i < count($expected); $i++) {
                if (strtolower(trim($headers[$i] ?? '')) !== strtolower($expected[$i])) {
                    $isClean = false;
                    break;
                }
            }
            Log::info('File uploaded', ['path' => $fullPath]);
            
            $importPath = $fullPath;
            
            Log::info('data diupload clean =', ['path' => $isClean]);
            if (!$isClean) {
                // Jalankan Python script untuk clean data
                $outputFile = Storage::path('CleanData/cleaned_' . time() . '.xlsx');
                $scriptPath = base_path('app/Python/Clean_data.py');
                $command = "python \"$scriptPath\" \"$fullPath\" \"$outputFile\"";
                exec($command, $output, $returnVar);

                Log::info('Python executed', ['command' => $command, 'output' => $output, 'return' => $returnVar]);

                if (!file_exists($outputFile)) {
                    throw new \Exception("File hasil clean tidak ditemukan.");
                }

                $importPath = $outputFile;
            }

            // Proses data clean ke database
            $sheet = IOFactory::load($importPath)->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $imported = 0;
            foreach ($data as $index => $row) {
                if ($index === 0) continue; // skip header

                $jam = strtolower(trim($row['C'] ?? ''));
                if ($jam !== 'rata rata') continue;

                $tanggal = $row['A'] ?? null;
                if (!$tanggal) continue;

                try {
                    $date = \Carbon\Carbon::parse($tanggal);
                } catch (\Exception $e) {
                    Log::warning('Tanggal tidak valid', ['val' => $tanggal]);
                    continue;
                }

                DailyEntry::create([
                    'date' => $date,
                    'avg_alb' => floatval(str_replace(',', '.', $row['D'] ?? 0)),
                    'avg_air' => floatval(str_replace(',', '.', $row['E'] ?? 0)),
                    'avg_kotoran' => floatval(str_replace(',', '.', $row['F'] ?? 0)),
                    'user_id' => $request->user()->id,
                ]);

                $imported++;
            }

            Log::info('Import selesai', ['baris_diimport' => $imported]);

            return redirect()->back()->with('success', "Berhasil mengimpor {$imported} baris data rata-rata.");

        } catch (\Exception $e) {
            Log::error('Gagal import', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    // Tampilkan form edit
public function edit($id)
{
    $entry = DailyEntry::findOrFail($id);
    return view('import.edit', compact('entry'));
}

// Proses update data
public function update(Request $request, $id)
{
    $request->validate([
        'date' => 'required|date',
        'avg_alb' => 'required|numeric',
        'avg_air' => 'required|numeric',
        'avg_kotoran' => 'required|numeric',
    ]);

    $entry = DailyEntry::findOrFail($id);
    $entry->update([
        'date' => $request->date,
        'avg_alb' => $request->avg_alb,
        'avg_air' => $request->avg_air,
        'avg_kotoran' => $request->avg_kotoran,
    ]);

    return redirect()->route('import.index')->with('success', 'Data berhasil diubah.');
}

// Hapus data
public function destroy($id)
{
    $entry = DailyEntry::findOrFail($id);

    // Contoh: cek relasi ke tabel lain
    $relatedCount = DB::table('related_table')->where('daily_entry_id', $entry->id)->count();

    if ($relatedCount > 0) {
        return redirect()->route('label.index')->with('error', 'Data tidak bisa dihapus karena sudah berelasi.');
    }

    $entry->delete();
    return redirect()->route('import.index')->with('success', 'Data berhasil dihapus.');
}

// Search/filter berdasarkan tanggal

}
