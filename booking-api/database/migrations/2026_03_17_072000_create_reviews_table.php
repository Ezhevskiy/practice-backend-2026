<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->unique();  // один отзыв на одно бронирование

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->tinyInteger('rating')
                  ->unsigned()
                  ->comment('от 1 до 5');

            $table->text('comment')->nullable();

            $table->timestamps();
            $table->softDeletes();  // для возможности "мягкого" удаления отзыва админом

            // Индексы для быстрого подсчёта среднего и сортировки
            $table->index(['booking_id']);
            $table->index(['user_id']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};