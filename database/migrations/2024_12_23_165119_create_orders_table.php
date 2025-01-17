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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number');
            $table->foreignId('partner_id')
                ->nullable()
                ->constrained('partners')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->nullOnDelete();
            $table->string('description')->nullable();
            $table->date('po_date');
            $table->date('start_date');
            $table->date('finish_date');

            $table->index('po_number');
            $table->index('deleted_at');
            $table->index('partner_id');
            $table->timestamps();
            $table->softDeletes();
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
