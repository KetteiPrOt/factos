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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('access_key', 49);
            $table->date('issuance_date');
            $table->string('number', 17);
            $table->string('status', 50);
            $table->text('content');
            $table->timestamps();

            // Foreign Keys
            $table->foreignId('user_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->unsignedTinyInteger('receipt_type_id')->nullable();
            $table->foreign('receipt_type_id')
                ->references('id')
                ->on('receipt_types')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
