<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('PK id from users table')->unsigned();
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->tinyInteger('gender')->nullable()->comment('0 = female, 1 = male');
            $table->string('address')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('mobile', 32);
            $table->string('license_no')->nullable();
            $table->string('restriction_codes')->nullable();
            $table->date('expiration_date')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
