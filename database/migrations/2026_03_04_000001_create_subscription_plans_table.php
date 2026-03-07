<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // e.g. "Monthly", "Yearly"
            $table->string('slug')->unique();           // e.g. "monthly", "yearly"
            $table->integer('duration_in_days');         // 30, 90, 180, 365
            $table->decimal('price', 10, 2);            // Plan price
            $table->text('description')->nullable();    // Optional description
            $table->boolean('is_active')->default(true); // Soft-disable plans
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
