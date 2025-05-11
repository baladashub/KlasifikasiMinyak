@extends('layouts.app')

@section('title', 'Import Data')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Import Data</h1>

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
                @if(session('validation_errors'))
                <ul class="mt-2 mb-0">
                    @foreach(session('validation_errors') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Upload File Excel</h5>
                    <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel (.xlsx, .xls)</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                            <div class="form-text">
                                Format file harus sesuai dengan template yang ditentukan.<br>
                                <strong>Ketentuan data yang valid:</strong>
                                <ul class="mb-0">
                                    <li>Tanggal harus valid dan tidak boleh kosong</li>
                                    <li>Nilai ALB, Air, dan Kotoran harus berupa angka positif</li>
                                    <li>Semua kolom harus terisi</li>
                                </ul>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Import Data</button>
                    </form>
                </div>
            </div>

            <form method="GET" action="{{ route('import.index') }}" class="flex items-center space-x-2 p-2 bg-white rounded-lg shadow-md mb-4">
                <input
                    type="date"
                    name="search_date"
                    value="{{ request('search_date') }}"
                    class="form-control w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition placeholder-gray-400"
                    placeholder="dd/mm/yyyy"
                    autocomplete="off">
                <button
                    type="submit"
                    class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md shadow transition">
                    <i class="bi bi-search mr-1"></i> Cari Tanggal
                </button>
                <a
                    href="{{ route('import.index') }}"
                    class="btn btn-secondary bg-gray-400 hover:bg-gray-500 text-white font-semibold px-4 py-2 rounded-md shadow transition">
                    Reset
                </a>
            </form>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Tabel Data Rata-rata</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>ALB</th>
                                    <th>Air</th>
                                    <th>Kotoran</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($entry->date)->format('d-m-Y') }}</td>
                                    <td>{{ $entry->avg_alb }}</td>
                                    <td>{{ $entry->avg_air }}</td>
                                    <td>{{ $entry->avg_kotoran }}</td>
                                    <td>{{ $entry->user_id }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-warning edit-btn me-2"
                                            data-id="{{ $entry->id }}"
                                            data-date="{{ \Carbon\Carbon::parse($entry->date)->format('Y-m-d') }}"
                                            data-alb="{{ $entry->avg_alb }}"
                                            data-air="{{ $entry->avg_air }}"
                                            data-kotoran="{{ $entry->avg_kotoran }}">
                                            Edit
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger delete-btn"
                                                data-id="{{ $entry->id }}">
                                                Hapus
                                            </button>
                                            </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('import.edit_modal')
@include('import.delete_modal')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit Modal
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-date').value = this.dataset.date;
            document.getElementById('edit-alb').value = this.dataset.alb;
            document.getElementById('edit-air').value = this.dataset.air;
            document.getElementById('edit-kotoran').value = this.dataset.kotoran;
            document.getElementById('editForm').action = '/import/' + this.dataset.id;
            editModal.show();
        });
    });

    // Delete Modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('deleteForm').action = '/import/' + this.dataset.id;
            deleteModal.show();
        });
    });
});
</script>
@endsection