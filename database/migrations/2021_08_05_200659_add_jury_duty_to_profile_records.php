<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJuryDutyToProfileRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_records', function (Blueprint $table) {
            $table->boolean('jury_duty')->after('is_fishing_license_valid');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_records', function (Blueprint $table) {
            $table->dropColumn(['jury_duty']);
        });
    }
}
