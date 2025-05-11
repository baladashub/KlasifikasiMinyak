@extends('layouts.app')

@section('title', 'Hasil Klasifikasi')
@section('content')
<div class="container">
    <h3 class="mb-4">Hasil Klasifikasi</h3>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Avg ALB</th>
                            <th>Avg Air</th>
                            <th>Avg Kotoran</th>
                            <th>Label</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($entry)
                        <tr>
                            <td>{{ $entry->date }}</td>
                            <td>{{ $entry->avg_alb }}</td>
                            <td>{{ $entry->avg_air }}</td>
                            <td>{{ $entry->avg_kotoran }}</td>
                            <td>{{ $entry->label }}</td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($accuracy)
    <div class="alert alert-info">
        <strong>Akurasi Model:</strong> {{ number_format($accuracy * 100, 2) }}%
    </div>
    @endif

    @if($confMatrix)
    <div class="card mt-4">
        <div class="card-header"><strong>Confusion Matrix</strong></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            @foreach(range(1, count($confMatrix)) as $i)
                                <th>Pred {{ $i }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($confMatrix as $i => $row)
                        <tr>
                            <th>Actual {{ $i+1 }}</th>
                            @foreach($row as $val)
                                <td>{{ $val }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
