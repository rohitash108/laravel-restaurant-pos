<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Table 1", "Table 3"
            $table->string('slug')->nullable(); // for URL e.g. table-3
            $table->string('floor')->nullable(); // 1st, 2nd
            $table->unsignedSmallInteger('capacity')->default(4);
            $table->string('status', 32)->default('available'); // available, occupied, reserved
            $table->timestamps();

            $table->unique(['restaurant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
