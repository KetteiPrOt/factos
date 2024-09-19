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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 25);
            $table->string('name', 255);
            $table->decimal('price', 8, 2)->unsigned(); // price between: 0.00 - 999 999 . 99
            $table->string('additional_info', 255)->nullable();
            $table->boolean('tourism_vat_applies')->default(false);
            $table->boolean('ice_applies')->default(false);
            $table->timestamps();

            // Foreign Keys
            $table->foreignId('user_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->unsignedSmallInteger('ice_type_id')->nullable();
            $table->foreign('ice_type_id')
                  ->references('id')
                  ->on('ice_types')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->unsignedTinyInteger('vat_rate_id')->nullable();
            $table->foreign('vat_rate_id')
                  ->references('id')
                  ->on('vat_rates')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
