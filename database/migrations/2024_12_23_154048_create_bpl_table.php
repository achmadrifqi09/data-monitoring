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
        Schema::create('bpl', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->float('volume')->nullable();
            $table->integer('partner_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->bigInteger('price')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('item_name');
            $table->index('unit');
            $table->index('partner_id');
            $table->index('order_id');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpl');
    }
};
