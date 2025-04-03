<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsInBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            
            $table->string('primary_school')->nullable();
            $table->string('primary_year_graduated')->nullable();

            $table->string('secondary_school')->nullable();
            $table->string('secondary_course')->nullable();
            $table->string('secondary_year_graduated')->nullable();

            $table->string('tertiary_school')->nullable();
            $table->string('tertiary_course')->nullable();
            $table->string('tertiary_year_graduated')->nullable();

            $table->string('other_school')->nullable();
            $table->string('other_course')->nullable();
            $table->string('other_year_graduated')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            //
        });
    }
}
