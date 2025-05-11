<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DataTrainingController extends Controller
{
    public function create()
    {
        return view('data-training.input');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            if ($request->file('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Store file
                $path = $file->storeAs('uploads', $fileName, 'public');
                
                // Read the Excel/CSV file
                $spreadsheet = IOFactory::load(storage_path('app/public/' . $path));
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Remove header row
                $header = array_shift($rows);
                
                // Process the data
                // TODO: Add your data processing logic here
                
                return redirect()->back()->with('success', 'File berhasil diunggah dan data sedang diproses.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('error', 'Tidak ada file yang diunggah.');
    }
} 