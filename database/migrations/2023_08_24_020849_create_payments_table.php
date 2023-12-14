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
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->comment('PK id from users table')->unsigned();
            $table->string('reference_no');
            $table->tinyInteger('mode_of_payment')->comment('0 = cash, 1 = paymongo')->default(0);
            $table->double('payment_amount', 10, 2)->default(0);
            $table->double('change_amount', 10, 2)->default(0);
            $table->tinyInteger('status')->comment('0 = pending, 1 = verified');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
