<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveExtraColumnsFromMedicalReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medical_reports', function (Blueprint $table) {
            $table->dropColumn(['allergies_latex', 'allergies_iodine', 'allergies_bromine', 'allergies_other']);

            $table->string('allergy_type', 16)
              ->nullable()
              ->after('medication_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medical_reports', function (Blueprint $table) {
          $table->boolean('allergies_latex')->nullable();
          $table->boolean('allergies_iodine')->nullable();
          $table->boolean('allergies_bromine')->nullable();
          $table->boolean('allergies_other')->nullable();

          $table->dropColumn('allergy_type');
        });
    }
}
