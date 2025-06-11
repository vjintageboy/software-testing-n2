<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Nhóm route có bảo vệ đăng nhập + xác thực
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard sau khi đăng nhập
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Quản lý khoa (faculties) - Livewire component
    Route::get('/admin/faculties', function () {
    return view('admin.faculties.index'); // Trỏ đến một file view mới
})->name('admin.faculties');

});
