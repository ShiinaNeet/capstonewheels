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
        Schema::create('audit_trails', function (Blueprint $table) {
            $category  = "0 = account, 1 = news, 2 = help, 3 = service, 4 = schedule, 5 = requirement, ";
            $category .= "6 = room, 7 = vehicle, 8 = database, 9 = enrollment, 10 = payment,";
            $category .= "11 = report, 12 = inquiry";
            $table->increments('id');
            $table->integer('action_user_id')->nullable()->comment('PK id from users table')->unsigned();
            $table->tinyInteger('category')->default(0)->comment($category);
            $table->tinyInteger('action_type')->default(0)->comment('0 = create, 1 = update, 2 = delete');
            $table->string('action_description');
            $table->timestamps();

            $table->foreign('action_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
