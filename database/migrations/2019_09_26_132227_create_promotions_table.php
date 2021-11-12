<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id')->nullable();
            $table->unsignedInteger('from_course')->nullable();
            $table->unsignedInteger('from_section')->nullable();
            $table->unsignedInteger('to_course')->nullable();
            $table->unsignedInteger('to_section')->nullable();
            $table->tinyInteger('grad')->nullable();
            $table->string('from_session')->nullable();
            $table->string('to_session')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_course')->references('id')->on('my_courses')->onDelete('cascade');
            $table->foreign('from_section')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('to_section')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('to_course')->references('id')->on('my_courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
