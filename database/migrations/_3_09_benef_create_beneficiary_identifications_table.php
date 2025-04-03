<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryIdentificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiary_identifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 255);
            
            $table->bigInteger('company_id');
            $table->bigInteger('beneficiary_id');

            $table->date('identification_date');

            $table->string('name', 255);
            $table->text('description')->nullable();

            $table->text('view'); // { index: 'default', front: 'default.front': back: 'default.back' }
            $table->text('options')->nullable();
            $table->text('content')->nullable();
            $table->text('approvals')->nullable();

            $table->text('left_signature')->nullable();
            $table->text('right_signature')->nullable();

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
        Schema::dropIfExists('beneficiary_identifications');
    }
}
