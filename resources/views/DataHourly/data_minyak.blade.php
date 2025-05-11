@extends('layouts.app')

@section('title', 'Data Minyak')
@section('content')

<div class="container">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-10 p-5 w-full" style=" min-height:100vh;">
            <h3 class="mb-4 text-2xl font-bold">KLASIFIKASI MINYAK</h3>
            <div class="mb-3">Form Input Klasifikasi CPO (Per 2 Jam)</div>

            {{-- ALERT SUKSES --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <button type="button" class="btn btn-info mb-2" onclick="isiDummy()">Isi Dummy</button>

            <form action="{{ route('hourly.store') }}" method="POST">
                @csrf
                <div class="d-flex justify-content-end mb-2">
                    <input type="date" class="form-control" name="date" style="width:auto;" required value="{{ $selectedDate ?? '' }}">
                </div>
                <div class="card w-full">
                    <div class="card-body p-0 ">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Jam</th>
                                        <th>ALB</th>
                                        <th>Kadar Air</th>
                                        <th>Kotoran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['08','10','12','14','16','18','20','22','00','02','04','06'] as $i => $jam)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control-plaintext text-black" readonly name="data[{{ $i }}][jam]" value="{{ $jam }}">
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control" name="data[{{ $i }}][alb]" required>
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control" name="data[{{ $i }}][air]" required>
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control" name="data[{{ $i }}][kotoran]" required>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <button class="btn btn-secondary mt-3" type="submit">Klasifikasi</button>
            </form>

            {{-- TAMPILKAN DATA HOURLY DARI DATABASE --}}
            @if(!empty($hourlyEntries) && count($hourlyEntries) > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <strong>Data Per Jam untuk Tanggal {{ $selectedDate }}</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Jam</th>
                                        <th>ALB</th>
                                        <th>Kadar Air</th>
                                        <th>Kotoran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hourlyEntries as $entry)
                                    <tr>
                                        <td>{{ $entry->jam }}</td>
                                        <td>{{ $entry->alb }}</td>
                                        <td>{{ $entry->air }}</td>
                                        <td>{{ $entry->kotoran }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
function isiDummy() {
     // Range Grade 1
     const albMin = 3.0, albMax = 3.5;
    const airMin = 0.1, airMax = 0.1;
    const kotoranMin = 0.1, kotoranMax = 0.1;

    document.querySelectorAll('tr').forEach(function(row) {
        const alb = row.querySelector('input[name*="[alb]"]');
        const air = row.querySelector('input[name*="[air]"]');
        const kotoran = row.querySelector('input[name*="[kotoran]"]');
        if (alb) alb.value = (Math.random() * (albMax - albMin) + albMin).toFixed(2);
        if (air) air.value = (Math.random() * (airMax - airMin) + airMin).toFixed(2);
        if (kotoran) kotoran.value = (Math.random() * (kotoranMax - kotoranMin) + kotoranMin).toFixed(2);
    });
}
</script>
@endsection
