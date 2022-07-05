<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_reports', function (Blueprint $table) {
          $table->bigIncrements('report_id');
          $table->bigInteger('profile_id');
          $table->string('citizen_id');
          $table->string('problem_started_at');
          $table->string('problem_description', 256);

          $table->string('problem_cause');
          $table->string('problem_cause_detail', 128)->nullable();

          $table->string('medical_history', 1024);

          $table->string('surgery_name', 32)->nullable();
          $table->string('surgery_year')->nullable();
          $table->string('surgery_complication', 128)->nullable();
          $table->string('surgery_description', 512)->nullable();

          $table->string('medication_name', 64)->nullable();
          $table->string('medication_done')->nullable();
          $table->string('medication_reason', 256)->nullable();
          $table->string('medication_description', 256)->nullable();

          $table->boolean('allergies_latex')->nullable();
          $table->boolean('allergies_iodine')->nullable();
          $table->boolean('allergies_bromine')->nullable();
          $table->boolean('allergies_other')->nullable();
          $table->string('allergies_details', 256)->nullable();

          $table->string('personal_views', 256);

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
        Schema::dropIfExists('medical_reports');
    }
}
