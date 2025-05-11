<?php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;
    use App\Models\DailyEntry;
    use Illuminate\Support\Facades\File;

    class LabeledController extends Controller
    {
        public function index(Request $request)
        {
            $query = DailyEntry::query();

            if ($request->filled('search')) {
                if (str_contains(strtolower($request->search), 'belum')) {
                    $query->whereNull('label');
                } else {
                    $query->where('label', 'like', "%{$request->search}%");
                }
            }
            if ($request->filled('search_date')) {
                $query->whereDate('date', $request->search_date);
            }

            $entries = $query->orderBy('date', 'desc')->paginate(10);
            return view('import.labelling', compact('entries'));
            
        }
        public function latihknn()
        {
            // Cek apakah ada data yang sudah dilabel
            $labeledData = DailyEntry::whereNotNull('label')->count();
            if ($labeledData === 0) {
                return redirect()->route('labeled.index')->with('error', 'Tidak ada data yang sudah dilabel. Silakan lakukan labelling terlebih dahulu.');
            }

            $python = 'C:\Users\dhyna\AppData\Local\Programs\Python\Python313\python.exe';
            $script = base_path('app/Python/LatihKnn.py');
            $command = "\"{$python}\" \"{$script}\" 2>&1";

            exec($command, $output, $status);

            Log::info("Command: {$command}");
            Log::info("Output:", $output);
            Log::info("Return status: {$status}");

            if ($status === 0) {
                // Baca file hasil training
                $jsonPath = base_path('app/Python/training_result.json');
                $plotPath = base_path('app/Python/knn_plot.png');
                
                if (file_exists($jsonPath) && file_exists($plotPath)) {
                    $result = json_decode(file_get_contents($jsonPath), true);
                    $accuracy = $result['accuracy'] ?? 0;
                    $bestK = $result['best_k'] ?? 0;
                    $bestCvAccuracy = $result['best_cv_accuracy'] ?? 0;
                    
                    // Pindahkan file plot ke storage
                    $filename = 'knn_plots/knn_plot_' . time() . '.png';
                    Storage::disk('public')->put($filename, file_get_contents($plotPath));
                    
                    $publicPath = Storage::url($filename);
                    
                    $message = "Training KNN berhasil dengan akurasi: " . number_format($accuracy * 100, 2) . "%";
                    $message .= " (Best k: {$bestK}, CV Accuracy: " . number_format($bestCvAccuracy * 100, 2) . "%)";
                    
                    return redirect()->route('labeled.index')
                        ->with('success', $message)
                        ->with('plot_path', $publicPath);
                } else {
                    return redirect()->route('labeled.index')->with('error', 'File hasil training tidak ditemukan.');
                }
            } else {
                return redirect()->route('labeled.index')->with('error', 'Training KNN gagal dijalankan.');
            }
        }
        public function store(Request $request, $id)
        {
            $request->validate([
                'label' => 'required|string|max:255'
            ]);
            $entry = DailyEntry::findOrFail($id);
            $entry->label = $request->label;
            $entry->save();

            return redirect()->route('labeled.index')->with('success', 'Label berhasil disimpan.');
        }
        public function autolabel()
        {
          
        
            // Cek apakah ada data di database
            $existingData = DailyEntry::count();
            if ($existingData === 0) {
                return redirect()->route('labeled.index')->with('error', 'Tidak ada data untuk dilabel. Silakan import data terlebih dahulu.');
            }

            // Cek apakah ada data yang belum dilabel
            $unlabeledData = DailyEntry::whereNull('label')->count();
            if ($unlabeledData === 0) {
                return redirect()->route('labeled.index')->with('error', 'Semua data sudah dilabel.');
            }

            $python = 'C:\Users\dhyna\AppData\Local\Programs\Python\Python313\python.exe';
            $script = base_path('app/Python/Labelling_data.py');
            $command = "\"{$python}\" \"{$script}\" 2>&1";

            exec($command, $output, $status);

            Log::info("Command: {$command}");
            Log::info("Output:", $output);
            Log::info("Return status: {$status}");

            if ($status === 0) {
                // Baca file hasil label
                $jsonPath = base_path('app/Python/label_output.json');
                if (file_exists($jsonPath)) {
                    $labels = json_decode(file_get_contents($jsonPath), true);

                    foreach ($labels as $item) {
                        DB::table('daily_entries')
                            ->where('id', $item['id'])
                            ->update(['label' => $item['label']]);
                    }

                    Log::info('Auto labelling berhasil dijalankan.');
                
                    return redirect()->route('labeled.index')->with('success', 'Auto labelling berhasil dijalankan.');
                } else {
                    Log::info('File label_output.json tidak ditemukan.');
                    return redirect()->route('labeled.index')->with('error', 'File label_output.json tidak ditemukan.');
                }
            } else {
                Log::info('Labelling gagal dijalankan.');
                return redirect()->route('labeled.index')->with('error', 'Labelling gagal dijalankan.');
            }
        }
        
        public function delete($id)
        {
            $entry = DailyEntry::findOrFail($id);
            $entry->label = null;
            $entry->save();

            return redirect()->route('labeled.index')->with('success', 'Label berhasil dihapus.');
        }
        //
    }
