<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_records', function (Blueprint $table) {
          $table->bigIncrements('profile_id');
          $table->string('full_name', 128);
          $table->string('designation', 64);
          $table->string('gender',16);
          $table->date('dob');
          $table->string('address', 512);
          $table->string('citizen_id', 32);
          $table->string('finger_print', 32);
          $table->string('dna_code', 32);
          $table->bigInteger('points');
          $table->boolean('is_driver_license_valid');
          $table->boolean('is_weapon_license_valid');
          $table->boolean('is_pilot_license_valid');
          $table->boolean('is_hunting_license_valid');
          $table->boolean('is_fishing_license_valid');

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
        Schema::dropIfExists('profile_records');
    }
}
