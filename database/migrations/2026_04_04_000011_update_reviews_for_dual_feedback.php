<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('reviewer_role', 20)->default('mentee')->after('mentee_id');
        });

        DB::table('reviews')->update([
            'reviewer_role' => 'mentee',
        ]);

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_session_id_unique');
            $table->unique(['session_id', 'reviewer_role']);
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['session_id', 'reviewer_role']);
            $table->unique('session_id');
            $table->dropColumn('reviewer_role');
        });
    }
};
