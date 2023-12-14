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
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('room_id')->nullable()->unsigned();
            $table->integer('vehicle_id')->nullable()->unsigned();
            $table->tinyInteger('type')->comment('0 = room, 1 = vehicle');
            $table->string('name');
            $table->text('description')->nullable();
            $table->double('price', 10, 2);
            $table->integer('duration')->comment('hours');
            $table->integer('max_capacity')->default(0);
            $table->string('requirements');
            $table->string('image')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
