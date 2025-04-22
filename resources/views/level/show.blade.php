@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <tr>
                    <th>ID</th>
                    <td>{{ $level->level_id }}</td>
                </tr>
                <tr>
                    <th>Nama Level</th>
                    <td>{{ $level->level_nama }}</td>
                </tr>
                <tr>
                    <th>Kode Level</th>
                    <td>{{ $level->level_kode }}</td>
                </tr>

            </table>

            <a href="{{ url('/level') }}" class="btn btn-secondary mt-3">Kembali</a>
        </div>
    </div>
@endsection