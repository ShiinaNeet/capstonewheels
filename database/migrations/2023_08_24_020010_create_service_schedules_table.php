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
        Schema::create('service_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('instructor_id')->comment('PK id from users table')->nullable()->unsigned();
            $table->integer('service_id')->comment('PK id from services table')->unsigned();
            $table->integer('batch')->default(0);
            $table->date('day_of_week');
            $table->time('time_start');
            $table->time('time_end');
            $table->integer('max_capacity')->default(0);
            $table->string('cancel_reason', 100)->nullable();
            $table->string('update_reason', 100)->nullable();
            $table->tinyInteger('status')->comment('0 = active, 1 = completed, 2 = cancelled')->default(0);
            $table->timestamps();

            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_schedules');
    }
};
