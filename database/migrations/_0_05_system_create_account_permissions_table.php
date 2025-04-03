<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('account_id');
			$table->bigInteger('module_id');
            $table->tinyInteger('access')->default(0);
            $table->tinyInteger('index')->default(0);
            $table->tinyInteger('store')->default(0);
            $table->tinyInteger('update')->default(0);
            $table->tinyInteger('destroy')->default(0);
            
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
        Schema::dropIfExists('account_permissions');
    }
}
