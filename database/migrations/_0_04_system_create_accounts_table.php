<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('account_type');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('mobile_number')->nullable();
            $table->longText('photo')->nullable();

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
        Schema::dropIfExists('accounts');
    }
}
