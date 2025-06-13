<?php

use Illuminate\Support\Facades\Route;

// Import các Controllers
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
|
| Đây là nơi bạn có thể đăng ký các web route cho ứng dụng của mình.
|
*/

// Trang chủ
Route::get('/', function () {
    return view('welcome');
});

// Các route xác thực (Laravel Breeze)
require __DIR__.'/auth.php';

// Nhóm route cho người dùng đã đăng nhập
Route::middleware(['auth'])->group(function () {
    
    // --- DASHBOARD & PROFILE ---
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // --- NHÓM QUẢN TRỊ (ADMIN) ---
    // Chỉ thêm tiền tố URL là '/admin', không thêm tiền tố tên route
    Route::prefix('admin')->group(function () {
        
        // --- Quản lý dữ liệu học vụ ---
        Route::resource('faculties', FacultyController::class);
        Route::resource('degrees', DegreeController::class);
        Route::resource('teachers', TeacherController::class);
        Route::resource('terms', TermController::class);
        Route::resource('courses', CourseController::class);
        Route::resource('classes', CourseClassController::class);
        Route::resource('assignments', AssignmentController::class);
        
        // --- Quản lý tính lương ---
        Route::resource('payroll-parameters', PayrollParameterController::class);
        Route::resource('class-size-coefficients', ClassSizeCoefficientController::class);
        Route::resource('payrolls', PayrollController::class)->except(['edit', 'update']);

        // --- Chức năng đặc biệt cho Lớp học phần ---
        Route::get('classes/bulk/create', [CourseClassController::class, 'createBulk'])->name('classes.create_bulk');
        Route::post('classes/bulk', [CourseClassController::class, 'storeBulk'])->name('classes.store_bulk');
    });
});
