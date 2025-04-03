<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyBarangayReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_barangay_reports', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id');

            $table->string('province_id');
			$table->string('city_id');
			$table->bigInteger('barangay_id');

            $table->integer('beneficiaries')->default(0);
            $table->integer('officers')->default(0);
            $table->integer('household')->default(0);
            $table->integer('priorities')->default(0);
            $table->integer('networks')->default(0);
            $table->integer('incentives')->default(0);
            $table->integer('patients')->default(0);
            $table->integer('assistances')->default(0);
            $table->integer('requested')->default(0);
            $table->integer('assisted')->default(0);

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
        Schema::dropIfExists('company_barangay_reports');
    }
}
