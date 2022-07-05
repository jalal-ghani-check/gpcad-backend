<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDuplicateColumnsFromCriminalRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('criminal_records', function (Blueprint $table) {
          $table->dropColumn('law_title');
          $table->dropColumn('law_code');
          $table->dropColumn('crime_type');
          $table->dropColumn('fine_amount');
          $table->dropColumn('jail_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('criminal_records', function (Blueprint $table) {
          $table->string('law_title');
          $table->string('crime_type', 32);
          $table->float('fine_amount', '11', '2');
          $table->integer('jail_time');
          $table->string('law_code', '8')->after('law_title');
        });
    }
}
