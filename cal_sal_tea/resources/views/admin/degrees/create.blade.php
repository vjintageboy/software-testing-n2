@extends('adminlte::page')

@section('title', 'Tạo Bằng cấp mới')

@section('content_header')
    <h1 class="m-0 text-dark">Tạo Bằng cấp mới</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('degrees.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Tên Bằng cấp</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Ví dụ: Tiến sĩ" value="{{ old('name') }}" required>
                        </div>
                        {{-- Thêm ô nhập liệu mới --}}
                        <div class="form-group">
                            <label for="abbreviation">Tên viết tắt</label>
                            <input type="text" name="abbreviation" class="form-control" id="abbreviation" placeholder="Ví dụ: TS" value="{{ old('abbreviation') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="coefficient">Hệ số</label>
                            <input type="number" step="0.01" name="coefficient" class="form-control" id="coefficient" placeholder="Ví dụ: 1.50" value="{{ old('coefficient') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="{{ route('degrees.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
