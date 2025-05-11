<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Klasifikasi Minyak</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    
    <div class="container">
        <h3 class="mb-4">Preview Laporan Data Minyak</h3>
    
        <form method="GET" action="{{ route('cetak.index') }}" class="row g-3 mb-4">
            <!-- Form bulan dan tahun di sini -->
        </form>
    
        @php
            use Carbon\Carbon;
            $groupedHourly = collect($entries)->groupBy('date');
            $dailyByDate = collect($entries)->keyBy('date');
            $jamList = ['08', '10', '12', '14', '16', '18', '20', '22', '24', '02', '04', '06'];
        @endphp
    
        @if($groupedHourly->count() > 0)
            @foreach($groupedHourly as $date => $dayEntries)
                @php
                    $carbonDate = Carbon::parse($date);
                    $hari = $carbonDate->isoFormat('dddd');
                    $tanggal = $carbonDate->format('d - m - Y');
                    $byJam = $dayEntries->keyBy('jam');
                    $daily = $dailyByDate[$date] ?? null;
                @endphp
    
                <div class="mb-4">
                    <strong>Hari : {{ ucfirst($hari) }}</strong><br>
                    <strong>Tanggal : {{ $tanggal }}</strong>
                    <table class="table table-bordered mt-2">
                        <thead>
                            <tr>
                                <th rowspan="2">NO</th>
                                <th rowspan="2">URAIAN</th>
                                <th rowspan="2">NORMA</th>
                                <th colspan="{{ count($jamList) }}">PERIODE PENGAMBILAN SAMPEL YANG DILAPORKAN</th>
                                <th rowspan="2">HASIL</th>
                                <th rowspan="2">LABEL</th>
                            </tr>
                            <tr>
                                @foreach($jamList as $jam)
                                    <th>{{ $jam }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>ALB CPO</td>
                                <td>&le; 3,50</td>
                                @foreach($jamList as $jam)
                                    <td>{{ $byJam[$jam]->alb ?? '-' }}</td>
                                @endforeach
                                <td>{{ $daily->avg_alb ?? '-' }}</td>
                                <td rowspan="3" style="vertical-align: middle;">{{ $daily->label ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Kadar Air CPO</td>
                                <td>&le; 0,20</td>
                                @foreach($jamList as $jam)
                                    <td>{{ $byJam[$jam]->air ?? '-' }}</td>
                                @endforeach
                                <td>{{ $daily->avg_air ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Kadar Kotoran CPO</td>
                                <td>&le; 0,02</td>
                                @foreach($jamList as $jam)
                                    <td>{{ $byJam[$jam]->kotoran ?? '-' }}</td>
                                @endforeach
                                <td>{{ $daily->avg_kotoran ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        @elseif(count($entries) > 0)
            @foreach($entries as $daily)
                @php
                    $carbonDate = Carbon::parse($daily->date);
                    $hari = $carbonDate->isoFormat('dddd');
                    $tanggal = $carbonDate->format('d - m - Y');
                @endphp
    
                <div class="mb-4">
                    <strong>Hari : {{ ucfirst($hari) }}</strong><br>
                    <strong>Tanggal : {{ $tanggal }}</strong>
                    <table class="table table-bordered mt-2">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>URAIAN</th>
                                <th>NORMA</th>
                                <th>HASIL</th>
                                <th>LABEL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>ALB CPO</td>
                                <td>&le; 3,50</td>
                                <td>{{ $daily->avg_alb ?? '-' }}</td>
                                <td rowspan="3" style="vertical-align: middle;">{{ $daily->label ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Kadar Air CPO</td>
                                <td>&le; 0,20</td>
                                <td>{{ $daily->avg_air ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Kadar Kotoran CPO</td>
                                <td>&le; 0,02</td>
                                <td>{{ $daily->avg_kotoran ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <p>Data tidak ditemukan.</p>
        @endif
    
       
    </div>
   
</body>
</html>
