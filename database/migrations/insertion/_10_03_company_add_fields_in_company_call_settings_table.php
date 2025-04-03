<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInCompanyCallSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_call_settings', function (Blueprint $table) {
            
            $table->tinyInteger('is_recording')->default(0);
            $table->string('recording_status_url', 255)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_call_settings', function (Blueprint $table) {
            //
        });
    }
}
