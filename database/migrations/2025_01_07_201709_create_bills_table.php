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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('netto');
            $table->string('bill_total');
            $table->string('bap');
            $table->date('date_of_bap');
            $table->string('dpp');
            $table->string('fee_deduction');
            $table->string('retention');
            $table->double('ppn_percentage');
            $table->string('ppn');
            $table->double('pph_percentage');
            $table->string('pph');
            $table->date('receipt_date');
            $table->integer('due_date');
            $table->timestamps();

            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
