<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warrants', function (Blueprint $table) {
          $table->bigIncrements('warrant_id');
          $table->string('title', 128);
          $table->string('description', 2048);
          $table->bigInteger('filed_against');
          $table->bigInteger('status_updated_by')->nullable();
          $table->string('status', '32');

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
        Schema::dropIfExists('warrants');
    }
}
