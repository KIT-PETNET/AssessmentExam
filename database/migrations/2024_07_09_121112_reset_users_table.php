<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class ResetUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Code to reverse the migration (if needed)
    }
}