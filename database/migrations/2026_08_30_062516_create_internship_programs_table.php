<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_programs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('slug')->unique();

            $table->string('title');

            $table->text('short_description')
                ->nullable();

            $table->longText('description')
                ->nullable();

            $table->date('registration_open_at');

            $table->date('registration_close_at');

            $table->date('program_start_at');

            $table->date('program_end_at');

            $table->timestamps();

            $table->softDeletes();

            // Index sesuai schema
            $table->index(
                [
                    'company_id',
                    'registration_open_at',
                    'registration_close_at',
                ],
                'idx_programs_company_dates'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_programs');
    }
};