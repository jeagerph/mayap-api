<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyNetworkSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_network_settings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id');

            $table->tinyInteger('master_degree_enabled')->default(1);
            $table->decimal('master_degree_points', 10, 2)->default(0.00);

            $table->tinyInteger('first_degree_enabled')->default(1);
            $table->decimal('first_degree_points', 10, 2)->default(0.00);

            $table->tinyInteger('second_degree_enabled')->default(1);
            $table->decimal('second_degree_points', 10, 2)->default(0.00);

            $table->tinyInteger('third_degree_enabled')->default(1);
            $table->decimal('third_degree_points', 10, 2)->default(0.00);

            $table->tinyInteger('fourth_degree_enabled')->default(1);
            $table->decimal('fourth_degree_points', 10, 2)->default(0.00);

            $table->tinyInteger('fifth_degree_enabled')->default(1);
            $table->decimal('fifth_degree_points', 10, 2)->default(0.00);

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
        Schema::dropIfExists('company_network_settings');
    }
}
