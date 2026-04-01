<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mentee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('call_room')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['mentor_id', 'mentee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
