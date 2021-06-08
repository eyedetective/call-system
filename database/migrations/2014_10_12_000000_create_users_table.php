<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username',30)->unique();
            $table->string('name',50)->nullable();
            $table->string('email',50)->unique()->nullable();
            $table->string('password');
            $table->string('api_token', 80)->unique();
            $table->string('permission', 80);
            $table->unsignedInteger('department')->nullable();
            $table->boolean('is_available')->default(false);
            $table->rememberToken();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
