<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use App\Models\HourlyEntry;
use App\Models\DailyEntry;
use App\Exports\LaporanCPOExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;


class CetakLaporanController extends Controller
{
        public function index(Request $request)
{
    $bulan = $request->input('bulan');
    $tahun = $request->input('tahun');

    $entries = [];
    $hourlyEntries = [];

    if ($bulan && $tahun) {
        $entries = DailyEntry::whereMonth('date', $bulan)
            ->whereYear('date', $tahun)
            ->orderBy('date')
            ->get();

        // Tambahkan ini untuk ambil data per jam
        $hourlyEntries = HourlyEntry::whereMonth('date', $bulan)
            ->whereYear('date', $tahun)
            ->orderBy('date')
            ->orderBy('jam')
            ->get();
    }

    // Kirim ke view
    return view('Cetak.cetakLaporan')
        ->with('entries', $entries)
        ->with('hourlyEntries', $hourlyEntries)
        ->with('bulan', $bulan)
        ->with('tahun', $tahun);
}
public function preview(Request $request)
{
    $bulan = $request->query('bulan');
    $tahun = $request->query('tahun');

    // Validasi input
    if (!$bulan || !$tahun) {
        return back()->with('error', 'Bulan dan tahun wajib diisi.');
    }

    // Ambil data berdasarkan bulan dan tahun
    $entries = DailyEntry::whereMonth('date', $bulan)
        ->whereYear('date', $tahun)
        ->get();

    $hourlyEntries = HourlyEntry::whereMonth('date', $bulan)
        ->whereYear('date', $tahun)
        ->orderBy('date')
        ->orderBy('jam')
        ->get();

    // Mengirim data ke halaman preview
    return view('Cetak.previewLaporan', compact('entries', 'hourlyEntries', 'bulan', 'tahun'));
}



public function exportLaporanCPO(Request $request)
{
    $bulan = $request->query('bulan');
    $tahun = $request->query('tahun');

    // Validasi input
    if (!$bulan || !$tahun) {
        return back()->with('error', 'Bulan dan tahun wajib diisi.');
    }

    // Format nama file
    $namaBulan = date("F", mktime(0, 0, 0, $bulan, 10)); // mengubah angka ke nama bulan
    $filename = "Laporan_CPO_{$tahun}_{$namaBulan}.xlsx";

    // Export dan unduh
    return Excel::download(new LaporanCPOExport($bulan, $tahun), $filename);
    }
    
}


