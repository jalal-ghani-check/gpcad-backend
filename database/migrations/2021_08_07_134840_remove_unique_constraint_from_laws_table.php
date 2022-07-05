<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueConstraintFromLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laws', function (Blueprint $table) {
          $table->string('name', 100)->change();
          $table->dropUnique('ps_laws_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laws', function (Blueprint $table) {
          $table->string('name', 32)->unique()->change();
        });
    }
}
