<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySmsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_sms_settings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id');

            $table->decimal('sms_credit', 10, 2)->default(0.00);

            $table->tinyInteger('sms_status')->default(0);
            $table->tinyInteger('otp_status')->default(0);

            $table->integer('max_char_per_sms')->default(159);
            $table->decimal('credit_per_branding_sms', 10, 2)->default(0.60);
            $table->decimal('credit_per_regular_sms', 10, 2)->default(0.40);
            $table->decimal('credit_threshold')->default(0.00);

            $table->string('header_name')->nullable();
            $table->string('footer_name')->nullable();

            $table->string('branding_api_url', 255)->nullable();
            $table->string('branding_api_code', 255)->nullable();

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
        Schema::dropIfExists('barangay_sms_settings');
    }
}
