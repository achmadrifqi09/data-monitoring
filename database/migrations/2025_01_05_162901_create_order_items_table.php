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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('item_id')
                ->constrained('items')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('bpl_number');
            $table->foreignId('partner_id')
                ->constrained('partners')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->double('volume');
            $table->string('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
