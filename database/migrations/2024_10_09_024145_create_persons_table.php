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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('identification', 13);
            $table->string('social_reason', 255);
            $table->string('email', 255);
            $table->char('phone_number', 10)->nullable();
            $table->string('address', 255)->nullable();
            $table->timestamps();
            // Foreign Keys
            $table->unsignedTinyInteger('identification_type_id');
            $table->foreign('identification_type_id')
                  ->references('id')
                  ->on('identification_types')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->foreignId('user_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
