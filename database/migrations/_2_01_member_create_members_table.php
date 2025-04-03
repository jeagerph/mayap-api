<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->bigInteger('company_id');

            $table->date('date_registered');

            $table->decimal('company_classification_id', 10, 2)->default(0.00);
            $table->string('province_id');
			$table->string('city_id');
			$table->bigInteger('barangay_id');
            $table->string('house_no')->nullable();

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->tinyInteger('gender');
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();

            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->tinyInteger('civil_status')->nullable();

            $table->longText('photo')->nullable();
            $table->longText('left_thumbmark')->nullable();
            $table->longText('right_thumbmark')->nullable();
            $table->longText('signature')->nullable();

            $table->tinyInteger('is_household')->default(0);
            $table->string('resident_type')->nullable();
            $table->string('precinct_no')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('religion')->nullable();
            $table->string('eligibility')->nullable();
            $table->string('blood_type')->nullable();

            $table->longText('health_history')->nullable();
            $table->longText('skills')->nullable();
            $table->longText('pending')->nullable();

            $table->string('gsis_sss_no')->nullable();
            $table->string('philhealth_no')->nullable();
            $table->string('pagibig_no')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('voters_no')->nullable();
            $table->string('organ_donor')->nullable();

            $table->string('primary_school')->nullable();
            $table->string('primary_year_graduated')->nullable();

            $table->string('secondary_school')->nullable();
            $table->string('secondary_course')->nullable();
            $table->string('secondary_year_graduated')->nullable();

            $table->string('tertiary_school')->nullable();
            $table->string('tertiary_course')->nullable();
            $table->string('tertiary_year_graduated')->nullable();

            $table->string('other_school')->nullable();
            $table->string('other_course')->nullable();
            $table->string('other_year_graduated')->nullable();

            $table->string('work_status')->nullable();
            $table->longText('work_experiences')->nullable();
            $table->decimal('monthly_income_start')->default(0.00);
            $table->decimal('monthly_income_end')->default(0.00);

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_address')->nullable();
            $table->string('emergency_contact_no')->nullable();

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
        Schema::dropIfExists('residents');
    }
}
