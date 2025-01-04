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
            $table->string('bpl_number')->unique();
            $table->integer('partner_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('description')->nullable();
            $table->date('date_of_use')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('bpl_number');
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
