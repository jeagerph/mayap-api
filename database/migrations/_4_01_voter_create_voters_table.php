<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voters', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->bigInteger('company_id');
            
            $table->string('code', 255);
            $table->date('date_registered');

            $table->string('province_id')->nullable();
			$table->string('city_id')->nullable();
			$table->bigInteger('barangay_id')->nullable();
            $table->string('house_no')->nullable();
            $table->string('address')->nullable();

            $table->string('first_name', 255);
            $table->string('middle_name', 255)->nullable();
            $table->string('last_name', 255);
            $table->tinyInteger('gender')->default(1);
            $table->date('date_of_birth', 255)->nullable();
            
            $table->string('precinct_no', 255)->nullable();
            $table->string('application_no', 255)->nullable();
            $table->date('application_date', 255)->nullable();
            $table->string('application_type', 255)->nullable();

            $table->text('remarks')->nullable();

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
        Schema::dropIfExists('voters');
    }
}
