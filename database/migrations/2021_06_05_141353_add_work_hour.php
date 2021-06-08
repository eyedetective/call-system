<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkHour extends Migration
{

    protected $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ($this->days as $key => $d) {
                $table->string($d.'_start',5)->nullable();
                $table->string($d.'_end',5)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ($this->days as $key => $d) {
                $table->dropColumn($d.'_start');
                $table->dropColumn($d.'_end');
            }
        });
    }
}
