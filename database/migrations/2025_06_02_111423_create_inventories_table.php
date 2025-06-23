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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_number', 15);
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->integer('beginning_inventory');
            $table->integer('ending_inventory');
            $table->date('starting_period');
            $table->date('ending_period');
            $table->integer('total_borrowed');
            $table->integer('usable_quantity');
            $table->integer('damaged_quantity');
            $table->integer('lost_quantity');
            $table->integer('repaired_qty');
            $table->integer('disposed_quantity');
            $table->foreignId('laboratory_id')->constrained('laboratories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
