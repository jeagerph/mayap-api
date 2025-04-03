<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiary_messages', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id');
            $table->bigInteger('beneficiary_id');

            $table->date('message_date');
            $table->tinyInteger('message_type'); // 1: REGULAR; 2: BRANDING;
            $table->string('message_sender_name');

            $table->string('mobile_number', 255);
            $table->text('message');

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
        Schema::dropIfExists('beneficiary_messages');
    }
}
