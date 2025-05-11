<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\DailyEntry;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
class LabelController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyEntry::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('label', 'like', "%{$search}%");
        }

        $entries = $query->orderBy('date', 'desc')->paginate(10);
        return view('import.labelling', compact('entries'));
    }
    public function store(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|string|max:255'
        ]);
        $entry = DailyEntry::findOrFail($id);
        $entry->label = $request->label;
        $entry->save();

        return redirect()->route('label.index')->with('success', 'Label berhasil disimpan.');
    }
  

    
    public function autolabel()
    {
    //     $python = 'C:\Users\dhyna\AppData\Local\Programs\Python\Python313\python.exe';
    // $script = base_path('app/Python/Labelling_data.py');
    // $command = "\"{$python}\" \"{$script}\" 2>&1";

    // exec($command, $output, $status);

    // Log::info("Command: {$command}");
    // Log::info("Output:", $output);
    // Log::info("Return status: {$status}");

    // if ($status === 0) {
    //     // Baca file hasil label
    //     $jsonPath = base_path('app/Python/label_output.json');
    //     if (file_exists($jsonPath)) {
    //         $labels = json_decode(file_get_contents($jsonPath), true);

    //         foreach ($labels as $item) {
    //             DB::table('daily_entries')
    //                 ->where('id', $item['id'])
    //                 ->update(['label' => $item['label']]);
    //         }

    //         Log::info('Auto labelling berhasil dijalankan.');
           
    //         return redirect()->route('label.index')->with('success', 'Auto labelling berhasil dijalankan.');
    //     } else {
    //         Log::info('File label_output.json tidak ditemukan.');
    //         return redirect()->route('label.index')->with('error', 'File label_output.json tidak ditemukan.');
    //     }
    // } else {
    //     Log::info('Labelling gagal dijalankan.');
    //     return redirect()->route('label.index')->with('error', 'Labelling gagal dijalankan.');
    // }
        
        return response()->json(['message' => 'Autolabelling triggered']);
      
    }
    public function delete($id)
    {
        $entry = DailyEntry::findOrFail($id);
        $entry->label = null;
        $entry->save();

        return redirect()->route('label.index')->with('success', 'Label berhasil dihapus.');
    }
}
