<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')
                ->constrained('internship_programs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('position_id')
                ->constrained('internship_positions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('intern_id')
                ->constrained('interns')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
            ])->default('pending');

            $table->text('rejection_reason')
                ->nullable();

            $table->timestamp('decided_at')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            /*
             * 1 lamaran aktif per posisi per intern.
             *
             * deleted_at ikut dalam unique key supaya
             * lamaran yang sudah di-soft-delete tidak
             * mengunci posisi selamanya.
             */
            $table->unique([
                'position_id',
                'intern_id',
                'deleted_at',
            ], 'uq_registrations_position_intern');

            $table->index([
                'program_id',
                'status',
            ]);

            $table->index('intern_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_registrations');
    }
};