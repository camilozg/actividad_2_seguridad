<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('first_name',20)->nullable();
            $table->string('last_name',40)->nullable();
            $table->string('dni',9)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone',12)->nullable();
            $table->string('address')->nullable();
            $table->enum('role', ['admin', 'planner', 'customer'])->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('iban',24)->nullable();
            $table->string('about',250)->nullable();
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
        Schema::dropIfExists('users');
    }
};
