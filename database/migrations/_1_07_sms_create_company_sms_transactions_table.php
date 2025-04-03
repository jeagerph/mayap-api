<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySmsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_sms_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');

            $table->bigInteger('company_id');

            $table->decimal('amount', 10, 2)->default(0.00);

            $table->longText('message');
            $table->integer('max_char_per_sms');
            $table->decimal('credit_per_sent', 10, 2); // AMOUNT PER SENT

            $table->tinyInteger('sms_type')->default(1); // 1: REGULAR; 2: BRANDING;
            
            $table->date('transaction_date');
            $table->tinyInteger('transaction_type'); // 1: INDIVIDUAL; 2: BULK;

            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();

            $table->tinyInteger('status')->default(1); // 1: PENDING; 2: SENT; 3: CANCELED
            $table->string('source')->nullable();

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
        Schema::dropIfExists('barangay_sms_transactions');
    }
}
