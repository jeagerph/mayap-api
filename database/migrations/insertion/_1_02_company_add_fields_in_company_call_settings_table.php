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
            
            $table->string('api_key', 255)->nullable();
            $table->string('api_secret', 255)->nullable();
            $table->string('app_sid', 255)->nullable();
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
