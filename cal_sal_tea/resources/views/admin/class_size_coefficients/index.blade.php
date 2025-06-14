@extends('adminlte::page')

@section('title', 'Hệ số theo Sĩ số')

@section('content_header')
    <h1 class="m-0 text-dark">Hệ số theo Sĩ số</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Thành công!</h5>
                            {{ session('success') }}
                        </div>
                    @endif

                    <a href="{{ route('class-size-coefficients.create') }}" class="btn btn-primary mb-2">
                        <i class="fa fa-plus"></i> Thêm quy tắc mới
                    </a>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Sĩ số tối thiểu</th>
                                <th>Sĩ số tối đa</th>
                                <th>Hệ số</th>
                                <th>Mô tả</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coefficients as $coefficient)
                                <tr>
                                    <td>{{ $coefficient->min_students }}</td>
                                    <td>{{ $coefficient->max_students }}</td>
                                    <td>{{ number_format($coefficient->coefficient, 2) }}</td>
                                    <td>{{ $coefficient->description }}</td>
                                    <td>
                                        <a href="{{ route('class-size-coefficients.edit', $coefficient) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('class-size-coefficients.destroy', $coefficient) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa quy tắc này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Không có dữ liệu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop