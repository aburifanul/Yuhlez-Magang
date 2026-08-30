<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('slug')->unique();

            $table->text('short_description')
                ->nullable();

            $table->longText('description')
                ->nullable();

            $table->string('photo')
                ->nullable();

            $table->string('whatsapp', 30)
                ->nullable();

            $table->string('contact_email')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->string('cv_path')
                ->nullable();

            $table->boolean('is_profile_complete')
                ->default(false);

            $table->timestamps();

            $table->softDeletes();

            $table->index('is_profile_complete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interns');
    }
};