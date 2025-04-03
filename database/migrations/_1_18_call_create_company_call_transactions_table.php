<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyCallTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_call_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');

            $table->bigInteger('company_id');

            $table->date('transaction_date');

            $table->decimal('amount', 10, 2)->default(0.00);

            $table->text('recording_url')->nullable();
            $table->string('mobile_number', 255);

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
        Schema::dropIfExists('company_call_transactions');
    }
}
