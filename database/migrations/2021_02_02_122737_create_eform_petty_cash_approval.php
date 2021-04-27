<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEformPettyCashApproval extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eform_petty_cash_approval', function (Blueprint $table) {
            $table->id();
            $table->string('profile')->nullable();
            $table->string('title')->nullable();
            $table->string('name')->nullable();
            $table->string('reason')->nullable();
            $table->string('action')->nullable();
            $table->integer('current_status_id')->nullable();
            $table->integer('action_status_id')->nullable();
            $table->integer('created_by');
            $table->integer('config_eform_id')->nullable();
            $table->integer('eform_petty_cash_id')->nullable();
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
        Schema::dropIfExists('eform_petty_cash_approval');
    }
}
