<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLatestProfileSearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('latest_profile_searches', function (Blueprint $table) {
          $table->bigInteger('profile_id');

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
        Schema::dropIfExists('latest_profile_search');
    }
}
