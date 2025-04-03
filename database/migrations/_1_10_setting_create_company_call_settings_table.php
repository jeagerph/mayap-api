<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyCallSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_call_settings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id');

            $table->decimal('call_credit', 10, 2)->default(0.00);
            $table->tinyInteger('call_status')->default(0);

            $table->string('account_sid', 255)->nullable();
            $table->string('auth_token', 255)->nullable();
            $table->string('auth_url', 255)->nullable();
            $table->string('phone_no', 255)->nullable();

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
        Schema::dropIfExists('company_call_settings');
    }
}
