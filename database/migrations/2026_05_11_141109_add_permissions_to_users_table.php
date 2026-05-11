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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_create_users')->default(false)->after('password');
            $table->boolean('can_manage_events')->default(false)->after('can_create_users');
            $table->boolean('can_access_trash')->default(false)->after('can_manage_events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_create_users', 'can_manage_events', 'can_access_trash']);
        });
    }
};
