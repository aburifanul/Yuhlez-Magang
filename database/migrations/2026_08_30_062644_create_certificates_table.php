<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('intern_id')
                ->constrained('interns')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('program_id')
                ->constrained('internship_programs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('file_path');

            $table->timestamp('generated_at')
                ->useCurrent();

            $table->timestamps();

            $table->unique([
                'intern_id',
                'program_id',
            ], 'uq_certificates_intern_program');

            $table->index('program_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};