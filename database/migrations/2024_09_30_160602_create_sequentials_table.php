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
        Schema::create('sequentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('next')->default(1);
            // Foreign keys
            $table->unsignedBigInteger('issuance_point_id');
            $table->foreign('issuance_point_id')
                  ->references('id')
                  ->on('issuance_points')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->unsignedTinyInteger('receipt_type_id');
            $table->foreign('receipt_type_id')
                  ->references('id')
                  ->on('receipt_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequentials');
    }
};
