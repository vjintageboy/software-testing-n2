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
    Schema::table('degrees', function (Blueprint $table) {
        $table->string('abbreviation', 20)->unique()->nullable()->after('name');
    });
}

public function down(): void
{
    Schema::table('degrees', function (Blueprint $table) {
        $table->dropColumn('abbreviation');
    });
}
};
