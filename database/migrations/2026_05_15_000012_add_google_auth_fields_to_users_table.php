<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'google_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('google_id')->nullable()->after('password');
            });
        }

        if (! Schema::hasIndex('users', ['google_id'], 'unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('google_id');
            });
        }

        if (! Schema::hasColumn('users', 'google_avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('google_avatar')->nullable()->after('google_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('users', ['google_id'], 'unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['google_id']);
            });
        }

        foreach (['google_avatar', 'google_id'] as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
