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
        Schema::table('class_size_coefficients', function (Blueprint $table) {
            // Thêm mới 2 cột `valid_from` và `valid_to`
            $table->date('valid_from')->after('coefficient')->nullable();
            $table->date('valid_to')->after('valid_from')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_size_coefficients', function (Blueprint $table) {
            // Xóa 2 cột này nếu cần rollback
            $table->dropColumn(['valid_from', 'valid_to']);
        });
    }
};