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
        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamp('planned_return_at');
            $table->timestamp('returned_at')->nullable();
            $table->unsignedInteger('return_quantity')->default(0);
            $table->enum('return_condition', ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])->nullable();
            $table->timestamps();
            
            $table->index('loan_id');
            $table->index('item_id');
            $table->index('planned_return_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};
