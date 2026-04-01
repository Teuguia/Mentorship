<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_mentor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['mentor_id', 'domain_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_mentor');
    }
};
