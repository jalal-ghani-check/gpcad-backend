<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleColumnToPoliceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('police_reports', function (Blueprint $table) {
            $table->string('report_title', 64)->after('report_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('police_reports', function (Blueprint $table) {
            $table->dropColumn('report_title');
        });
    }
}
