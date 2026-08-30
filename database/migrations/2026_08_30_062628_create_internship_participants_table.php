<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')
                ->constrained('internship_programs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('intern_id')
                ->constrained('interns')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamp('joined_at')
                ->useCurrent();

            $table->timestamp('removed_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'program_id',
                'intern_id',
            ], 'uq_participants_program_intern');

            $table->index('intern_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_participants');
    }
};