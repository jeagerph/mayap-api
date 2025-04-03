<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySmsCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_sms_credits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');

            $table->bigInteger('company_id');

            $table->date('credit_date');
            $table->tinyInteger('credit_mode'); // 1: REPLENISH; 2: WITHDRAWAL;

            $table->decimal('amount', 10, 2);

            $table->longText('remarks')->nullable();

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
        Schema::dropIfExists('barangay_sms_credits');
    }
}
