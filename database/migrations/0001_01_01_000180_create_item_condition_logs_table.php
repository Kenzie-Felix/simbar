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
        Schema::create('item_condition_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->enum('old_condition', ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])->nullable();
            $table->enum('new_condition', ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang']);
            $table->string('reason', 255)->nullable();
            $table->foreignId('changed_by_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            
            $table->index('item_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_condition_logs');
    }
};
