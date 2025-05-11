@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <h3 class="mb-4">Dashboard</h3>
    <div class="row mb-4">
        <div class="col-md-4">
            <a href="{{ route('hourly.index') }}" style="text-decoration: none;">
                <div class="card text-white bg-primary mb-3" style="cursor:pointer;">
                    <div class="card-body">
                        <h5 class="card-title">Data Minyak (Hourly)</h5>
                        <p class="card-text display-6">{{ $totalHourly }}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('import.index') }}" style="text-decoration: none;">
                <div class="card text-white bg-success mb-3" style="cursor:pointer;">
                    <div class="card-body">
                        <h5 class="card-title">Data Training (Daily)</h5>
                        <p class="card-text display-6">{{ $totalDaily }}</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Tambah card lain jika perlu -->
    </div>
 
    <div class="card mt-4">
        <div class="card-header">
            <strong>Distribusi Data Label</strong>
        </div>
        <div class="card-body">
            <canvas id="labelChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labelCounts = @json($labelCounts);
        const labels = Object.keys(labelCounts);
        const data = Object.values(labelCounts);

        const ctx = document.getElementById('labelChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Data',
                    data: data,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</div>
@endsection 