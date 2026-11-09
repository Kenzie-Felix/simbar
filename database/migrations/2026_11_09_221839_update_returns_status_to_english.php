<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, change the enum column to string with sufficient length
        Schema::table('returns', function (Blueprint $table) {
            $table->string('status', 50)->change();
        });

        // Update all return statuses from Indonesian to English
        DB::table('returns')->update([
            'status' => DB::raw("CASE 
                WHEN status = 'Menunggu Verifikasi' THEN 'Pending Verification'
                WHEN status = 'Terima' THEN 'Accepted'
                WHEN status = 'Tolak' THEN 'Rejected'
                ELSE status
            END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert all return statuses from English back to Indonesian
        DB::table('returns')->update([
            'status' => DB::raw("CASE 
                WHEN status = 'Pending Verification' THEN 'Menunggu Verifikasi'
                WHEN status = 'Accepted' THEN 'Terima'
                WHEN status = 'Rejected' THEN 'Tolak'
                ELSE status
            END")
        ]);

        // Revert the column back to enum
        Schema::table('returns', function (Blueprint $table) {
            $table->enum('status', ['Menunggu Verifikasi', 'Terima', 'Tolak'])->change();
        });
    }
};
