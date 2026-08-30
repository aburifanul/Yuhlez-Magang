<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('work_id')
                ->constrained('works')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('intern_id')
                ->constrained('interns')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamp('added_at')
                ->useCurrent();

            $table->timestamp('removed_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'work_id',
                'intern_id',
            ], 'uq_work_members_work_intern');

            $table->index('intern_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_members');
    }
};