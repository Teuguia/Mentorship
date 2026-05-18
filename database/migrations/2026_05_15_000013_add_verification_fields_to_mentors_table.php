<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mentors', 'verification_status')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->string('verification_status')->default('pending')->after('profile_photo');
            });
        }

        if (! Schema::hasColumn('mentors', 'verification_document')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->binary('verification_document')->nullable()->after('verification_status');
            });
        }

        if (! Schema::hasColumn('mentors', 'verification_document_name')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->string('verification_document_name')->nullable()->after('verification_document');
            });
        }

        if (! Schema::hasColumn('mentors', 'verification_document_mime')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->string('verification_document_mime')->nullable()->after('verification_document_name');
            });
        }

        if (! Schema::hasColumn('mentors', 'verification_document_size')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->unsignedInteger('verification_document_size')->nullable()->after('verification_document_mime');
            });
        }

        if (! Schema::hasColumn('mentors', 'linkedin_url')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->string('linkedin_url')->nullable()->after('verification_document_size');
            });
        }

        if (! Schema::hasColumn('mentors', 'portfolio_url')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->string('portfolio_url')->nullable()->after('linkedin_url');
            });
        }

        if (! Schema::hasColumn('mentors', 'verification_notes')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->text('verification_notes')->nullable()->after('portfolio_url');
            });
        }

        if (! Schema::hasColumn('mentors', 'verified_at')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->timestamp('verified_at')->nullable()->after('verification_notes');
            });
        }

        if (! Schema::hasColumn('mentors', 'verified_by')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('mentors', 'verified_by')) {
            Schema::table('mentors', function (Blueprint $table) {
                $table->dropConstrainedForeignId('verified_by');
            });
        }

        foreach ([
            'verified_at',
            'verification_notes',
            'portfolio_url',
            'linkedin_url',
            'verification_document_size',
            'verification_document_mime',
            'verification_document_name',
            'verification_document',
            'verification_status',
        ] as $column) {
            if (Schema::hasColumn('mentors', $column)) {
                Schema::table('mentors', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
