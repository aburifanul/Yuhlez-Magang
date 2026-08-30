<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_positions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')
                ->constrained('internship_programs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('name');

            $table->unsignedInteger('quota')
                ->default(1);

            $table->timestamps();

            $table->softDeletes();

            $table->index('program_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_positions');
    }
};