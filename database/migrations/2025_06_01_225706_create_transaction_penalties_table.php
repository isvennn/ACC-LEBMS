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
        Schema::create('transaction_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('amount', 10, 2)->default(0.0);
            $table->enum('status', ['Lost', 'Damaged', 'Late Return']);
            $table->enum('remarks', ['Replace', 'Pay']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_penalties');
    }
};
