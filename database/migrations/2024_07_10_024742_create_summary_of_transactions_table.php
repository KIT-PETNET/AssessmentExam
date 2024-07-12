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
            $table->string('Type');
            $table->string('Name');
            $table->string('Description');
            $table->decimal('commission', 8, 2);
            $table->decimal('gross_commission', 8, 2);
            $table->decimal('net_commission', 8, 2);
            $table->boolean('vatable')->default(false);
            $table->timestamps();
        });

        Schema::table('summary_of_transactions', function (Blueprint $table) {
            $table->decimal('total_gross_commission_vatable', 8, 2)->nullable();
            $table->decimal('total_net_commission_vatable', 8, 2)->nullable();
            $table->decimal('total_gross_commission_non_vatable', 8, 2)->nullable();
            $table->decimal('total_net_commission_non_vatable', 8, 2)->nullable();
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
        Schema::table('summary_of_transactions', function (Blueprint $table) {
            $table->dropColumn('total_gross_commission_vatable');
            $table->dropColumn('total_net_commission_vatable');
            $table->dropColumn('total_gross_commission_non_vatable');
            $table->dropColumn('total_net_commission_non_vatable');
        });
    }
}