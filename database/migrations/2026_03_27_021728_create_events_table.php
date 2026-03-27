<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->binary('id', 16)->primary();

            $table->string('name', 80)->nullable();
            $table->string('monogram', 40)->nullable();
            $table->string('typography', 40)->nullable();
            $table->smallInteger('template')->nullable();
            $table->binary('album', 16);
            $table->string('song', 50)->nullable();
            $table->string('watermark', 50)->nullable();
            $table->date('date');

            // users.id es bigint unsigned, así que aquí también debe ser compatible
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};