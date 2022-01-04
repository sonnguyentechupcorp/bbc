<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderBirthDateRoleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('gender')->after('name')->nullable();
            $table->dateTime('birth_date')->after('gender')->nullable();
            $table->json('role')->after('birth_date');
            $table->string('avatar')->after('email')->nullable();
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
            Schema::dropIfExists('gender');
            Schema::dropIfExists('birth_date');
            Schema::dropIfExists('role');
            Schema::dropIfExists('avatar');
        });
    }
}
