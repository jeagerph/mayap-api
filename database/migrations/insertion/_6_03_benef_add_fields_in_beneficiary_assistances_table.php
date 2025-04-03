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
            
            $table->tinyInteger('is_assisted')->default(0);
            $table->date('assisted_date')->nullable();
            $table->string('assisted_by', 255)->nullable();

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
