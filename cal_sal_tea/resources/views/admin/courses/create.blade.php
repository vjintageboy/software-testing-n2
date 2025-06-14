@extends('adminlte::page')

@section('title', 'Thêm mới Học phần')

@section('content_header')
    <h1 class="m-0 text-dark">Thêm mới Học phần</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Nhập thông tin" theme="success" icon="fas fa-plus">
                <form action="{{ route('courses.store') }}" method="POST">
                    @csrf
                    <x-adminlte-input name="name" label="Tên Học phần" placeholder="Nhập tên học phần"
                        fgroup-class="col-md-6" value="{{ old('name') }}" required/>
                    <x-adminlte-input name="course_code" label="Mã Học phần" placeholder="Nhập mã học phần"
                        fgroup-class="col-md-6" value="{{ old('course_code') }}" required/>
                    <x-adminlte-input type="number" name="credits" label="Số tín chỉ" placeholder="Nhập số tín chỉ"
                        fgroup-class="col-md-6" value="{{ old('credits') }}" min="0" required/>

                    {{-- Thêm trường chọn Khoa --}}
                    <x-adminlte-select name="faculty_id" label="Khoa" fgroup-class="col-md-6" required>
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="fas fa-university"></i>
                            </div>
                        </x-slot>
                        <option value="">-- Chọn Khoa --</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                    
                    <div class="col-12">
                        <x-adminlte-button type="submit" label="Thêm mới" theme="success" icon="fas fa-save"/>
                        <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
