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
        Schema::create('issuance_points', function (Blueprint $table) {
            $table->id();
            $table->char('code', 3);
            $table->string('description', 255)->nullable();
            $table->boolean('active')->default(true);
            // Foreign keys
            $table->unsignedBigInteger('establishment_id');
            $table->foreign('establishment_id')
                  ->references('id')
                  ->on('establishments')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuance_points');
    }
};
