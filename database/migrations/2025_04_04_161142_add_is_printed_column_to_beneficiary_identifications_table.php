<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPrintedColumnToBeneficiaryIdentificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beneficiary_identifications', function (Blueprint $table) {
            $table->tinyInteger('is_printed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beneficiary_identifications', function (Blueprint $table) {
            $table->dropColumn('is_printed');
        });
    }
}
