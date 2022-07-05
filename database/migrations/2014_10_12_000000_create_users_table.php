<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('full_name', 512)->charset('utf8')->collate("utf8_unicode_ci");
            $table->string('username', 128)->unique()->charset('utf8');
            $table->string('gender')->nullable();
            $table->string('citizen_id');
            $table->string('profile_picture',512);
            $table->bigInteger('role_id')->unsigned();
            $table->bigInteger('rank_id')->unsigned();
            $table->bigInteger('department_id')->unsigned();
            $table->string('call_sign');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->charset('utf8');
            $table->string('password_salt')->charset('utf8');
            $table->rememberToken();

            /* Common Columns */
            $table->softDeletes();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
