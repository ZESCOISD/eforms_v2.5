<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEformSubsistenceAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eform_subsistence_account', function (Blueprint $table) {

            $table->id();

            $table->integer('creditted_account_id')->nullable();
            $table->string('creditted_amount')->nullable();
            $table->integer('debitted_account_id')->nullable();
            $table->string('debitted_amount')->nullable();
            $table->integer('eform_subsistence_id')->nullable();

            $table->string('subsistence_code')->nullable();

            $table->integer('status_id')->nullable();
            $table->string('account')->nullable();
            $table->string('company')->nullable();
            $table->string('intra_company')->nullable();
            $table->string('project')->nullable();
            $table->string('pems_project')->nullable();
            $table->string('spare')->nullable();
            $table->string('description')->nullable();

            $table->string('hod_code')->nullable();
            $table->string('hod_unit')->nullable();
            $table->string('ca_code')->nullable();
            $table->string('ca_unit')->nullable();
            $table->string('hrm_code')->nullable();
            $table->string('hrm_unit')->nullable();
            $table->string('expenditure_code')->nullable();
            $table->string('expenditure_unit')->nullable();
            $table->string('dr_code')->nullable();
            $table->string('dr_unit')->nullable();
            $table->string('audit_code')->nullable();
            $table->string('audit_unit')->nullable();

            $table->integer('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eform_subsistence_account');
    }
}
