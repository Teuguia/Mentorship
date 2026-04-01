<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->string('expertise_title');
            $table->unsignedInteger('years_experience')->default(0);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->text('availability')->nullable();
            $table->string('profile_photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentors');
    }
};
