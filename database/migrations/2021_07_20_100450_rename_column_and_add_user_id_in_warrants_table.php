<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnAndAddUserIdInWarrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warrants', function (Blueprint $table) {
            $table->dropColumn('filed_against');
            $table->bigInteger('profile_id')->after('description');
            $table->bigInteger('user_id')->after('warrant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warrants', function (Blueprint $table) {
          $table->bigInteger('filed_against')->after('description');
          $table->dropColumn('profile_id');
          $table->dropColumn('user_id');
        });
    }
}
