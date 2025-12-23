<?php

use App\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users');
            $table->string('symbol');
            $table->enum('side', ['buy', 'sell']);
            $table->decimal('price', 8, 2)->default(0);
            $table->decimal('amount', 8, 2)->default(0);
            $table->enum('status', [
                OrderStatus::Open->value,
                OrderStatus::Filled->value,
                OrderStatus::Cancelled->value,
            ])->default(OrderStatus::Open->value);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
