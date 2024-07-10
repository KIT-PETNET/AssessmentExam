<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummaryOfTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_of_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->decimal('commission', 8, 2);
            $table->decimal('gross_commission', 8, 2);
            $table->decimal('net_commission', 8, 2);
            $table->boolean('vatable')->default(false);
            $table->timestamps();
            $table->foreign('transaction_id')->references('id')->on('datacommission')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summary_of_transactions');
    }
}
