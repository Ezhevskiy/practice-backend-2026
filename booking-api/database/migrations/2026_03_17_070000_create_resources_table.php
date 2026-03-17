<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);                    // "Переговорка А-101", "Большой зал", etc.
            $table->text('description')->nullable();        // подробное описание, оснащение

            $table->unsignedInteger('capacity');            // вместимость (кол-во человек), обязательно
            $table->unsignedInteger('floor')->nullable();   // этаж (может отсутствовать)

            // Характеристики / удобства (булевы флаги — удобно для фильтрации)
            $table->boolean('has_projector')->default(false);
            $table->boolean('has_whiteboard')->default(false);
            $table->boolean('has_video_conference')->default(false);
            $table->boolean('has_monitor')->default(false);
            $table->boolean('has_speakers')->default(false);

            $table->decimal('price_per_hour', 8, 2)->nullable();  // цена за час, если есть почасовая оплата

            $table->boolean('is_active')->default(true);    // можно временно скрывать комнату

            $table->timestamps();

            // Полезные индексы для будущих запросов
            $table->index('is_active');
            $table->index('capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};