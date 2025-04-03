<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyInvoiceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_invoice_settings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id');

            $table->decimal('domain_hosting', 10, 2)->default(1000);
            $table->decimal('branding_sms', 10, 2)->default(0.60);
            $table->decimal('regular_sms', 10, 2)->default(0.40);
            $table->decimal('virtual_storage', 10, 2)->default(1000);

            $table->tinyInteger('show_left_representative')->default(1);
            $table->longText('left_representative_name')->nullable();
            $table->longText('left_representative_position')->nullable();

            $table->tinyInteger('show_right_representative')->default(0);
            $table->longText('right_representative_name')->nullable();
            $table->longText('right_representative_position')->nullable();

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
        Schema::dropIfExists('barangay_invoice_settings');
    }
}
