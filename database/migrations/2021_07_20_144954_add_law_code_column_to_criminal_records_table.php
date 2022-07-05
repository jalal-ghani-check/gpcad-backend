<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLawCodeColumnToCriminalRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('criminal_records', function (Blueprint $table) {
          $table->string('law_code', '8')->after('law_title');
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
          $table->dropColumn('law_code');
        });
    }
}
