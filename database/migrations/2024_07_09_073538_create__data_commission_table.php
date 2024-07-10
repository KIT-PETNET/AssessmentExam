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
        Schema::create('DataCommission', function (Blueprint $table) {
            $table->id();
            $table->string('Type');
            $table->string('Name');
            $table->text('Description');
            $table->integer('Charges');
            $table->integer('Amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DataCommission');
    }
};
