<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\Log;
use App\Models\DailyEntry;
use App\Models\HourlyEntry;
use Illuminate\Http\Request;

class LaporanCPOExport implements FromView, WithTitle
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $entries = DailyEntry::whereMonth('date', $this->bulan)
            ->whereYear('date', $this->tahun)
            ->get();

        $hourlyEntries = HourlyEntry::whereMonth('date', $this->bulan)
            ->whereYear('date', $this->tahun)
            ->orderBy('date')
            ->orderBy('jam')
            ->get();

        return view('Cetak.previewLaporan', [
            'entries' => $entries,
            'hourlyEntries' => $hourlyEntries,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
        ]);
    }

    public function title(): string
    { 
        $title = "Lprn-{$this->bulan}-{$this->tahun}";
        Log::info("Sheet title: $title (length: " . strlen($title) . ")");
        return $title;
    }
}
