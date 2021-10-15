<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->unsignedInteger('from_class')->nullable();
            $table->unsignedInteger('from_section')->nullable();
            $table->unsignedInteger('to_class')->nullable();
            $table->unsignedInteger('to_section')->nullable();
            $table->tinyInteger('grad')->nullable();
            $table->string('from_session')->nullable();
            $table->string('to_session')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_class')->references('id')->on('my_classes')->onDelete('cascade');
            $table->foreign('from_section')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('to_section')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('to_class')->references('id')->on('my_classes')->onDelete('cascade');
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
