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
        Schema::create('bill_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')
                ->references('id')
                ->on('bills')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('document');
            $table->timestamps();

            $table->index('bill_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_documents');
    }
};
