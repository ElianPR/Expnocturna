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
        Schema::table('camera_animations', function (Blueprint $table) {
            $table->dropColumn(['mov_file', 'webm_file']);
            $table->string('mp4_file')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('camera_animations', function (Blueprint $table) {
            $table->string('mov_file')->nullable();
            $table->string('webm_file')->nullable();
            $table->dropColumn('mp4_file');
        });
    }
};
