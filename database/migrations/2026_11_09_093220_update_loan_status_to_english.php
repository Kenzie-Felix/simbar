<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all loan statuses from Indonesian to English
        DB::table('loans')->update([
            'status' => DB::raw("CASE 
                WHEN status = 'Menunggu Persetujuan' THEN 'Pending Approval'
                WHEN status = 'Disetujui' THEN 'Approved'
                WHEN status = 'Ditolak' THEN 'Rejected'
                WHEN status = 'Dipinjam' THEN 'Borrowed'
                WHEN status = 'Dikembalikan' THEN 'Returned'
                ELSE status
            END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert all loan statuses from English back to Indonesian
        DB::table('loans')->update([
            'status' => DB::raw("CASE 
                WHEN status = 'Pending Approval' THEN 'Menunggu Persetujuan'
                WHEN status = 'Approved' THEN 'Disetujui'
                WHEN status = 'Rejected' THEN 'Ditolak'
                WHEN status = 'Borrowed' THEN 'Dipinjam'
                WHEN status = 'Returned' THEN 'Dikembalikan'
                ELSE status
            END")
        ]);
    }
};
