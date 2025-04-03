<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInCompanySmsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_sms_settings', function (Blueprint $table) {
            
            $table->tinyInteger('report_status')->default(0);
            $table->string('report_template', 255)->nullable();
            $table->text('report_mobile_numbers')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_sms_settings', function (Blueprint $table) {
            //
        });
    }
}
