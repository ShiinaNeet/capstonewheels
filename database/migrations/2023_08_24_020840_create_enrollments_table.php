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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id')->comment('PK id from services table')->unsigned();
            $table->integer('student_id')->comment('PK id from users table')->unsigned();
            $table->integer('batch')->default(0);
            $table->tinyInteger('status')->comment('0 = cancelled, 1 = active, 2 = finished, 3 = pending')->default(0);
            $table->string('ltms');
            $table->string('aces');
            $table->string('ccm');
            $table->timestamps();
            $table->string('certificate_status', 255)->comment('pending,release,unreleased')->default('pending');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
