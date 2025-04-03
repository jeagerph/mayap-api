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
            
            $table->tinyInteger('birthday_status')->default(0);
            $table->text('birthday_header', 255)->nullable();
            $table->text('birthday_message', 255)->nullable();

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
