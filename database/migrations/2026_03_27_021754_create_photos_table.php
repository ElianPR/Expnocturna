<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->binary('id', 16)->primary();

            $table->string('url', 50);
            $table->binary('id_event', 16);

            $table->foreign('id_event')
                ->references('id')
                ->on('events')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};