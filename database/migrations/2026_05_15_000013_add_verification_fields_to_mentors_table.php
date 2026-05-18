<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            $table->string('verification_status')->default('pending')->after('profile_photo');
            $table->binary('verification_document')->nullable()->after('verification_status');
            $table->string('verification_document_name')->nullable()->after('verification_document');
            $table->string('verification_document_mime')->nullable()->after('verification_document_name');
            $table->unsignedInteger('verification_document_size')->nullable()->after('verification_document_mime');
            $table->string('linkedin_url')->nullable()->after('verification_document_size');
            $table->string('portfolio_url')->nullable()->after('linkedin_url');
            $table->text('verification_notes')->nullable()->after('portfolio_url');
            $table->timestamp('verified_at')->nullable()->after('verification_notes');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn([
                'verification_status',
                'verification_document',
                'verification_document_name',
                'verification_document_mime',
                'verification_document_size',
                'linkedin_url',
                'portfolio_url',
                'verification_notes',
                'verified_at',
            ]);
        });
    }
};
