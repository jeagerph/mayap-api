<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyDocumentTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_document_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 255);

            $table->bigInteger('company_id');

            $table->string('name', 255);
            $table->text('description')->nullable();

            $table->longText('view');
            $table->longText('options')->nullable();
            $table->longText('content')->nullable();
            $table->longText('inputs')->nullable();
            $table->longText('tables')->nullable();
            $table->longText('approvals')->nullable();

            $table->longText('header_border')->nullable();
            $table->longText('left_signature')->nullable();
            $table->longText('right_signature')->nullable();

            $table->tinyInteger('enabled')->default(1);
            
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
        Schema::dropIfExists('company_document_templates');
    }
}
