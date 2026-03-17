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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('resource_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->dateTime('starts_at');
            $table->dateTime('ends_at');

            $table->enum('status', [
                'pending',      // ждёт подтверждения (если нужно)
                'confirmed',    // подтверждено
                'cancelled',    // отменено
                'completed',    // завершено (прошло время окончания)
            ])->default('confirmed');

            $table->text('notes')->nullable();              // заметки пользователя или админа

            $table->timestamps();

            // Очень важные индексы для быстрой проверки пересечений
            $table->index(['resource_id', 'starts_at', 'ends_at']);
            $table->index('user_id');
            $table->index('status');

            // Можно добавить проверку starts_at < ends_at на уровне БД (не во всех версиях MySQL/MariaDB работает)
            // $table->check('starts_at < ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};