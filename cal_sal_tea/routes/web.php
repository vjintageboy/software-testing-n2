<?php

use Illuminate\Support\Facades\Route;

// Import các Controllers cho từng module
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\DegreeController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseClassController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\PayrollParameterController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\ClassSizeCoefficientController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', fn () => view('welcome'));

// Các route xác thực (Laravel Breeze)
require __DIR__.'/auth.php';

// Nhóm route cho người dùng đã đăng nhập
Route::middleware(['auth'])->group(function () {
    
    // --- DASHBOARD & PROFILE ---
    
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::prefix('admin')->group(function () {
        // Quản lý thông tin cá nhân
        Route::get('settings', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('settings', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('settings', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // --- NHÓM QUẢN LÝ DỮ LIỆU HỌC VỤ ---
        
        Route::resource('faculties', FacultyController::class);               // Khoa
        Route::resource('degrees', DegreeController::class);                 // Bằng cấp
        Route::resource('teachers', TeacherController::class);              // Giáo viên
        Route::resource('terms', TermController::class);                    // Kỳ học
        Route::resource('courses', CourseController::class);                // Học phần / Môn học
        Route::resource('classes', CourseClassController::class);           // Lớp học phần
        Route::resource('assignments', AssignmentController::class);        // Phân công giảng dạy
        

        // --- NHÓM QUẢN LÝ TÍNH LƯƠNG ---
        
        Route::resource('payroll-parameters', PayrollParameterController::class);  // Tham số tính lương
        Route::resource('class-size-coefficients', ClassSizeCoefficientController::class); // Hệ số sĩ số
        Route::resource('payrolls', PayrollController::class)->except(['edit', 'update']); // Bảng lương

        // --- CHỨC NĂNG TẠO NHANH LỚP HỌC PHẦN ---
        
        Route::get('classes/bulk/create', [CourseClassController::class, 'createBulk'])->name('classes.create_bulk');
        Route::post('classes/bulk', [CourseClassController::class, 'storeBulk'])->name('classes.store_bulk');
    });
});
