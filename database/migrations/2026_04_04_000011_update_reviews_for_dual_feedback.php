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
        $this->ensureReviewerRoleColumn();

        DB::table('reviews')->update([
            'reviewer_role' => 'mentee',
        ]);

        $this->ensureSessionIdIndex();
        $this->dropLegacySessionUniqueIndex();
        $this->ensureSessionReviewerUniqueIndex();
    }

    public function down(): void
    {
        $this->dropSessionReviewerUniqueIndex();

        if (! Schema::hasIndex('reviews', 'reviews_session_id_unique', 'unique')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unique('session_id', 'reviews_session_id_unique');
            });
        }

        if (Schema::hasColumn('reviews', 'reviewer_role')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn('reviewer_role');
            });
        }
    }

    private function ensureReviewerRoleColumn(): void
    {
        if (Schema::hasColumn('reviews', 'reviewer_role')) {
            return;
        }

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

    private function ensureSessionIdIndex(): void
    {
        if (Schema::hasIndex('reviews', 'reviews_session_id_index')) {
            return;
        }

        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table->index('session_id', 'reviews_session_id_index');
            });
        } catch (QueryException $exception) {
            if (! $this->isMysqlError($exception, 1061)) {
                throw $exception;
            }
        }
    }

    private function dropLegacySessionUniqueIndex(): void
    {
        if (! Schema::hasIndex('reviews', 'reviews_session_id_unique', 'unique')) {
            return;
        }

        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropUnique('reviews_session_id_unique');
            });
        } catch (QueryException $exception) {
            if (! $this->isMysqlError($exception, 1553)) {
                if ($this->isMysqlError($exception, 1091)) {
                    return;
                }

                throw $exception;
            }

            $this->dropSessionForeignKeys();
            $this->ensureSessionIdIndex();
            $this->dropLegacySessionUniqueIndexAfterForeignKeys();
            $this->ensureSessionForeignKey();
        }
    }

    private function dropLegacySessionUniqueIndexAfterForeignKeys(): void
    {
        if (! Schema::hasIndex('reviews', 'reviews_session_id_unique', 'unique')) {
            return;
        }

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

    private function ensureSessionReviewerUniqueIndex(): void
    {
        if (Schema::hasIndex('reviews', ['session_id', 'reviewer_role'], 'unique')) {
            return;
        }

        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unique(['session_id', 'reviewer_role'], 'reviews_session_id_reviewer_role_unique');
            });
        } catch (QueryException $exception) {
            if (! $this->isMysqlError($exception, 1061)) {
                throw $exception;
            }
        }
    }

    private function dropSessionReviewerUniqueIndex(): void
    {
        if (! Schema::hasIndex('reviews', 'reviews_session_id_reviewer_role_unique', 'unique')
            && ! Schema::hasIndex('reviews', ['session_id', 'reviewer_role'], 'unique')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_session_id_reviewer_role_unique');
        });
    }

    private function dropSessionForeignKeys(): void
    {
        $names = $this->foreignKeyNamesForColumn('reviews', 'session_id');

        if ($names === []) {
            $names = ['reviews_session_id_foreign'];
        }

        foreach (array_unique($names) as $name) {
            try {
                Schema::table('reviews', function (Blueprint $table) use ($name) {
                    $table->dropForeign($name);
                });
            } catch (QueryException $exception) {
                if (! $this->isMysqlError($exception, 1091)) {
                    throw $exception;
                }
            }
        }
    }

    private function ensureSessionForeignKey(): void
    {
        if ($this->hasForeignKeyForColumn('reviews', 'session_id')) {
            return;
        }

        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table
                    ->foreign('session_id', 'reviews_session_id_foreign')
                    ->references('id')
                    ->on('sessions')
                    ->cascadeOnDelete();
            });
        } catch (QueryException $exception) {
            if (! in_array((int) ($exception->errorInfo[1] ?? 0), [121, 1826], true)) {
                throw $exception;
            }
        }
    }

    private function hasForeignKeyForColumn(string $table, string $column): bool
    {
        return $this->foreignKeyNamesForColumn($table, $column) !== [];
    }

    private function foreignKeyNamesForColumn(string $table, string $column): array
    {
        try {
            return collect(Schema::getForeignKeys($table))
                ->filter(fn (array $foreignKey) => in_array($column, $foreignKey['columns'], true))
                ->pluck('name')
                ->filter()
                ->values()
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    private function isMysqlError(QueryException $exception, int $code): bool
    {
        return (int) ($exception->errorInfo[1] ?? 0) === $code;
    }
};
