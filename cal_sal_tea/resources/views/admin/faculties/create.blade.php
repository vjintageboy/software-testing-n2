    @extends('adminlte::page')

    @section('title', 'Tạo Khoa mới')

    @section('content_header')
        <h1 class="m-0 text-dark">Tạo Khoa mới</h1>
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

                        <form action="{{ route('faculties.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Tên Khoa</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Nhập tên khoa" value="{{ old('name') }}" >
                            </div>
                            <div class="form-group">
                                <label for="abbreviation">Tên viết tắt</label>
                                <input type="text" name="abbreviation" class="form-control" id="abbreviation" placeholder="Ví dụ: CNTT" value="{{ old('abbreviation') }}" >
                            </div>
                            <div class="form-group">
                                <label for="description">Mô tả</label>
                                <textarea name="description" class="form-control" id="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" dusk="submit-create-faculty">Lưu</button>
                            <a href="{{ route('faculties.index') }}" class="btn btn-secondary">Hủy</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @stop
    