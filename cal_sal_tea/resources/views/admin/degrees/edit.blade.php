@extends('adminlte::page')

@section('title', 'Chỉnh sửa Bằng cấp')

@section('content_header')
    <h1 class="m-0 text-dark">Chỉnh sửa Bằng cấp</h1>
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

                    <form action="{{ route('degrees.update', $degree) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Tên Bằng cấp</label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $degree->name) }}" required>
                        </div>
                        {{-- Thêm ô nhập liệu mới --}}
                         <div class="form-group">
                            <label for="abbreviation">Tên viết tắt</label>
                            <input type="text" name="abbreviation" class="form-control" id="abbreviation" value="{{ old('abbreviation', $degree->abbreviation) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="coefficient">Hệ số</label>
                            <input type="number" step="0.01" name="coefficient" class="form-control" id="coefficient" value="{{ old('coefficient', $degree->coefficient) }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('degrees.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
