<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('slug')->unique();

            $table->string('name');

            $table->text('short_description')
                ->nullable();

            $table->longText('description')
                ->nullable();

            $table->string('logo')
                ->nullable();

            $table->string('whatsapp', 30)
                ->nullable();

            $table->string('contact_email')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->text('gmap_embed')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};