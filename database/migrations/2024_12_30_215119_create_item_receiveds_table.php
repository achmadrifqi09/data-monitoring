<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_receiveds', function (Blueprint $table) {
            $table->id();
            $table->string('bpl_number');
            $table->foreignId('item_id')
                ->constrained('items')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('order_id');
            $table->double('amount_received');
            $table->string('nominal');
            $table->date('date_received');
            $table->timestamps();

            $table->index('bpl_number');
            $table->index('item_id');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_receiveds');
    }
};
