<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Thêm cột faculty_id sau cột 'id'
            // Cho phép null để học phần có thể không thuộc khoa nào
            // onDelete('set null') sẽ set faculty_id thành NULL nếu khoa bị xóa
            $table->foreignId('faculty_id')->nullable()->after('id')->constrained('faculties')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Xóa ràng buộc khóa ngoại trước khi xóa cột
            $table->dropForeign(['faculty_id']);
            $table->dropColumn('faculty_id');
        });
    }
};
