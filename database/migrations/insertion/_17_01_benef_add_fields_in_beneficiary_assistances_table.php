<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInBeneficiaryAssistancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beneficiary_assistances', function (Blueprint $table) {
            
            $table->string('province_id')->nullable();
			$table->string('city_id')->nullable();
			$table->bigInteger('barangay_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beneficiary_assistances', function (Blueprint $table) {
            //
        });
    }
}
