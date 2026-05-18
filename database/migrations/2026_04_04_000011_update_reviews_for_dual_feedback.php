<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('reviews', 'reviewer_role')) {
            try {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->string('reviewer_role', 20)->default('mentee')->after('mentee_id');
                });
            } catch (QueryException $exception) {
                if (! $this->isMysqlError($exception, 1060)) {
                    throw $exception;
                }
            }
        }

        DB::table('reviews')->update([
            'reviewer_role' => 'mentee',
        ]);

        if (Schema::hasIndex('reviews', 'reviews_session_id_unique', 'unique')) {
            try {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->dropUnique('reviews_session_id_unique');
                });
            } catch (QueryException $exception) {
                if (! $this->isMysqlError($exception, 1091)) {
                    throw $exception;
                }
            }
        }

        if (! Schema::hasIndex('reviews', ['session_id', 'reviewer_role'], 'unique')) {
            try {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->unique(['session_id', 'reviewer_role']);
                });
            } catch (QueryException $exception) {
                if (! $this->isMysqlError($exception, 1061)) {
                    throw $exception;
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('reviews', ['session_id', 'reviewer_role'], 'unique')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropUnique(['session_id', 'reviewer_role']);
            });
        }

        if (! Schema::hasIndex('reviews', 'reviews_session_id_unique', 'unique')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unique('session_id');
            });
        }

        if (Schema::hasColumn('reviews', 'reviewer_role')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn('reviewer_role');
            });
        }
    }

    private function isMysqlError(QueryException $exception, int $code): bool
    {
        return (int) ($exception->errorInfo[1] ?? 0) === $code;
    }
};
