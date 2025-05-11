@extends('layouts.app')

@section('title', '')

@section('content')
<div class="container">
    <h3 class="mb-4">Cetak Laporan Data Minyak</h3>
    <form method="GET" action="{{ route('cetak.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="bulan" class="form-label">Bulan</label>
            <select name="bulan" id="bulan" class="form-control" required>
                <option value="">-- Pilih Bulan --</option>
                @foreach(range(1,12) as $b)
                    <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="tahun" class="form-label">Tahun</label>
            <select name="tahun" id="tahun" class="form-control" required>
                <option value="">-- Pilih Tahun --</option>
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <a href="{{ route('cetak.index') }}" class="btn btn-secondary">Reset</a>
            <a href="{{ route('export.preview', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}" 
   class="btn btn-info" target="_blank">
    👀 Preview Laporan
</a>

        </div>
        
        <div class="col-md-3 align-self-end">
            @if(isset($entries) && count($entries) > 0)
            <a href="{{ route('export.laporanCPO', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
           class="btn btn-success">
            📥 Download Excel
        </a>
            @endif
        </div>
    </form>

    @if(isset($entries))
    <div class="card">
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
                        @forelse($entries as $entry)
                        <tr>
                            <td>{{ $entry->date }}</td>
                            <td>{{ $entry->avg_alb }}</td>
                            <td>{{ $entry->avg_air }}</td>
                            <td>{{ $entry->avg_kotoran }}</td>
                            <td>{{ $entry->label }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data untuk bulan dan tahun ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
