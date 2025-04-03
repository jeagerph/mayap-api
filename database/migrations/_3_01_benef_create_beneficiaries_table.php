<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id');

            $table->string('code', 255);
            $table->date('date_registered');

            $table->string('province_id');
			$table->string('city_id');
			$table->bigInteger('barangay_id');
            $table->string('house_no')->nullable();
            $table->string('address')->nullable();

            $table->string('first_name', 255);
            $table->string('middle_name', 255)->nullable();
            $table->string('last_name', 255);
            $table->tinyInteger('gender');
            $table->string('mobile_no', 255);
            $table->string('email', 255)->nullable();
            
            $table->string('place_of_birth', 255)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('civil_status', 255)->nullable();
            $table->string('citizenship', 255)->nullable();
            $table->string('religion', 255)->nullable();

            $table->string('educational_attainment', 255)->nullable();
            $table->string('occupation', 255)->nullable();
            $table->string('monthly_income', 255)->nullable();
            $table->string('source_of_income', 255)->nullable();
            $table->string('classification', 255)->nullable();

            $table->tinyInteger('is_household')->default(0);
            $table->integer('household_count')->default(0);
            $table->tinyInteger('is_priority')->default(0);

            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('emergency_contact_address', 255)->nullable();
            $table->string('emergency_contact_no', 255)->nullable();

            $table->longText('photo')->nullable();

            $table->decimal('incentive')->default(0.00);
            $table->integer('rating')->default(0);

            $table->text('remarks')->nullable();

            // Default fields
            $table->dateTime('created_at');
            $table->bigInteger('created_by');
            $table->dateTime('updated_at')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();

            $table->index(['first_name', 'middle_name', 'last_name'], 'idx_name');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beneficiaries');
    }
}
