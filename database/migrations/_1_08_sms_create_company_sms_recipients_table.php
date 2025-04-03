<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySmsRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_sms_recipients', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_sms_transaction_id');

            $table->string('mobile_number'); // MOBILE NUMBER
            $table->longText('message'); // MESSAGE

            $table->tinyInteger('status')->default(0); // 0: NOT SENT; 1: SENT; 2: FAILED
            $table->string('status_code')->nullable(); // DIAFAAN/ITEXMO status code
            $table->dateTime('sent_at')->nullable(); 
            $table->longText('failure_message')->nullable();

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
        Schema::dropIfExists('barangay_sms_recipients');
    }
}
