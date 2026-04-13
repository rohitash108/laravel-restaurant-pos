<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_remaining', 16, 6);
            $table->date('expiry_date')->nullable();
            $table->date('received_at')->nullable();
            $table->string('reference', 128)->nullable();
            $table->decimal('unit_cost', 14, 6)->nullable();
            $table->timestamps();

            $table->index(['ingredient_id', 'branch_id']);
            $table->index(['branch_id', 'expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_batches');
    }
};
