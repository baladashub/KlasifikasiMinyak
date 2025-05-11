@extends('layouts.app')

@section('title', 'Labelling Data')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Data Labelling</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info">
                        {{ session('info') }}
                    </div>
                    @endif

                    <div class="mb-3">
                        <form action="{{ route('labeled.autolabel') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">Auto Labelling</button>
                        </form>
                        <form action="{{ route('labeled.latihknn') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Latih KNN</button>
                        </form>

                        <form action="{{ route('labeled.index') }}" method="GET" class="d-flex gap-2 align-items-center mb-3">
                            <div class="input-group" style="max-width: 300px;">
                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="Cari label..."
                                    value="{{ request('search') }}"
                                >
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <div class="input-group" style="max-width: 200px;">
                                <input
                                    type="date"
                                    name="search_date"
                                    class="form-control"
                                    value="{{ request('search_date') }}"
                                    id="searchDateInput"
                                    style="min-width: 120px;"
                                >
                                <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('searchDateInput').showPicker()">
                                    <i class="bi bi-calendar"></i>
                                </button>
                            </div>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Cari Tanggal
                            </button>
                            <a href="{{ route('labeled.index') }}" class="btn btn-secondary">Reset</a>
                        </form>

                    </div>


                    <div class="table-responsive">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Avg ALB</th>
                                    <th>Avg Air</th>
                                    <th>Avg Kotoran</th>
                                    <th>Label</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entries as $entry)
                                <tr>
                                    <td>{{ $entry->id }}</td>
                                    <td>{{ $entry->date }}</td>
                                    <td>{{ $entry->avg_alb }}</td>
                                    <td>{{ $entry->avg_air }}</td>
                                    <td>{{ $entry->avg_kotoran }}</td>
                                    <td>
                                        @if($entry->label)
                                            {{ $entry->label }}
                                        @else
                                            Belum Dilabel
                                        @endif
                                    </td>
                                        
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $entry->id }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('labeled.delete', $entry->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus label ini?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $entry->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $entry->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $entry->id }}">Edit Label</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('labeled.store', $entry->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="label{{ $entry->id }}" class="form-label">Label</label>
                                                        <select class="form-control" id="label{{ $entry->id }}" name="label" required>
                                                            <option value="">-- Pilih Grade --</option>
                                                            <option value="Grade 1" {{ $entry->label == 'Grade 1' ? 'selected' : '' }}>Grade 1</option>
                                                            <option value="Grade 2" {{ $entry->label == 'Grade 2' ? 'selected' : '' }}>Grade 2</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $entries->links() }}
                </div>
            </div>

            @if(session('plot_path'))
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">Hasil Training KNN</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <img src="{{ session('plot_path') }}" alt="KNN Plot" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection