<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInCompanyCallTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_call_transactions', function (Blueprint $table) {
            
            $table->integer('call_duration')->default(0);
            $table->string('call_sid', 255)->nullable();
            $table->string('call_status', 255)->nullable();

            $table->string('recording_sid', 255)->nullable();
            $table->integer('recording_duration')->default(0);
            $table->string('recording_status', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_call_transactions', function (Blueprint $table) {
            //
        });
    }
}
