<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriminalRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criminal_records', function (Blueprint $table) {
          $table->bigIncrements('record_id');
          $table->bigInteger('police_report_id');
          $table->bigInteger('profile_record_id');
          $table->bigInteger('law_id');
          $table->string('law_title');
          $table->string('crime_type', 32);
          $table->float('fine_amount', '11', '2');
          $table->integer('jail_time');

          // common columns
          $table->bigInteger('created_by')->nullable();
          $table->bigInteger('updated_by')->nullable();
          $table->timestamps();
          $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('criminal_records');
    }
}
