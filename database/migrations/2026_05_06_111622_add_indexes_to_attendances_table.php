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
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('attendance_date');
            $table->index('user_id');
            $table->index(['user_id', 'attendance_date']); // Compound index for common lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['attendance_date']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['user_id', 'attendance_date']);
        });
    }
};
