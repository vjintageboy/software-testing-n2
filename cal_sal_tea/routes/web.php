<?php

use Illuminate\Support\Facades\Route;

// Import các Controllers cho từng module
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\DegreeController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\CourseClassController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\PayrollParameterController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ClassSizeCoefficientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route trang chủ
Route::get('/', function () {
    return view('welcome');
});

// Các route được tạo bởi Laravel Breeze cho việc xác thực
require __DIR__.'/auth.php';

// Nhóm các route cho trang quản trị, yêu cầu người dùng phải đăng nhập
Route::middleware(['auth'])->group(function () {
    
    // Route cho trang Dashboard (bảng điều khiển)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Route cho trang quản lý thông tin cá nhân (profile)
    Route::get('admin/settings', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('admin/settings', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('admin/settings', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- NHÓM CÁC ROUTE QUẢN LÝ DỮ LIỆU ---
    // Sử dụng Route::resource sẽ tự động tạo các route cho các hành động:
    // index, create, store, show, edit, update, destroy
    
    // Quản lý Khoa (Faculties)
    Route::resource('faculties', FacultyController::class);

    // Quản lý Bằng Cấp (Degrees)
    Route::resource('degrees', DegreeController::class);

    // Quản lý Giáo viên (Teachers)
    Route::resource('teachers', TeacherController::class);
    
    // Quản lý Kì học (Terms)
    Route::resource('terms', TermController::class);

    // Quản lý Học phần / Môn học (Courses)
    Route::resource('courses', CourseController::class);

    // Quản lý Lớp học phần (Classes)
   Route::resource('classes', CourseClassController::class);

    // Quản lý Phân công giảng dạy (Assignments)
    Route::resource('assignments', AssignmentController::class);

    // --- NHÓM ROUTE TÍNH LƯƠNG & THAM SỐ ---

    // Quản lý tham số tính lương (Payroll Parameters)
    Route::resource('payroll-parameters', PayrollParameterController::class);

    // Quản lý bảng lương (Payrolls)
   Route::resource('payrolls', PayrollController::class)->except(['edit', 'update']); // Bảng lương chỉ có thể tạo, xem và xóa, không sửa.
    
    Route::resource('class-size-coefficients', ClassSizeCoefficientController::class);

});

