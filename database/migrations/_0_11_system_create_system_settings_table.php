<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('sms_service_status')->default(1);

            $table->string('branding_api_url', 255)->nullable();
            $table->string('branding_api_code', 255)->nullable();

            $table->string('diafaan_otp_host', 255)->nullable();
            $table->string('diafaan_otp_db', 255)->nullable();
            $table->string('diafaan_otp_username', 255)->nullable();
            $table->string('diafaan_otp_password', 255)->nullable();
            $table->string('diafaan_otp_port', 255)->nullable();

            $table->tinyInteger('call_service_status')->default(1);

            $table->string('call_account_sid', 255)->nullable();
            $table->string('call_auth_token', 255)->nullable();
            $table->string('call_auth_url', 255)->nullable();
            $table->string('call_phone_no', 255)->nullable();

            $table->tinyInteger('is_default')->default(1);

            // Default fields
            $table->dateTime('created_at');
            $table->bigInteger('created_by');
            $table->dateTime('updated_at')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
}
