<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInBeneficiaryPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beneficiary_patients', function (Blueprint $table) {
            
            $table->tinyInteger('status')->default(1); // 1: FOR APPROVAL; 2: IN PROGRES; 3: COMPLETED; 4: CANCELED

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beneficiary_patients', function (Blueprint $table) {
            //
        });
    }
}
