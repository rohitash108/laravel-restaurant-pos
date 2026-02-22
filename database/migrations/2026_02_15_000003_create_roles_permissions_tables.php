<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('name');
            $table->json('permissions')->nullable(); // {"dashboard":{"view":true},"orders":{"view":true,"create":true,"edit":true,"delete":true}, ...}
            $table->timestamps();

            $table->unique(['restaurant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
