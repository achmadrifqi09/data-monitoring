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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('bpl_number');
            $table->foreign('bpl_number')
                ->references('bpl_number')
                ->on('bpl')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->float('volume')->nullable();
            $table->string('price')->nullable();
            $table->string('brand')->nullable();
            $table->string('specification')->nullable();
            $table->boolean('is_selected')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('bpl_number');
            $table->index('item_name');
            $table->index('brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
