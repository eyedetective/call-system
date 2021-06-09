<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name',100);
            $table->string('customer_phone',30);
            $table->unsignedInteger('topic_id');
            $table->text('comment')->nullable();
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->string('call_status',50)->nullable();
            $table->string('status',50)->nullable();
            $table->string('call_durations',50)->nullable();
            $table->string('ip')->nullable();
            $table->string('source')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('referenceCall')->nullable();
            $table->string('outgoingCall')->nullable();
            $table->unsignedInteger('rep_user_id')->nullable();
            $table->unsignedInteger('assign_user_id')->nullable();
            $table->dateTime('schedule_datetime')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
