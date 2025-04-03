<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiary_calls', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id');
            $table->bigInteger('beneficiary_id');

            $table->date('call_date');
            $table->decimal('call_minutes', 10, 2)->default(0.00);
            $table->text('call_url')->nullable();

            $table->string('mobile_number', 255);

            $table->tinyInteger('status')->default();

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
        Schema::dropIfExists('beneficiary_calls');
    }
}
