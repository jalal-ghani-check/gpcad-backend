<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoliceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('police_reports', function (Blueprint $table) {
          $table->bigIncrements('report_id');
          $table->bigInteger('profile_id');
          $table->bigInteger('user_id');
          $table->string('case_number', 16);
          $table->string('cid', 16);
          $table->string('ref_case_number', 16)->nullable();

          $table->string('description', 1024);

          $table->string('officers_involved',128);
          $table->boolean('shorts_fired');
          $table->boolean('gsr_test_result');
          $table->boolean('casing_recovered');

          $table->string('suspects_involved',128);
          $table->boolean('use_of_violence');
          $table->boolean('med_treatment');
          $table->boolean('legal_aid');

          $table->string('items_seized',128);

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
        Schema::dropIfExists('police_reports');
    }
}
