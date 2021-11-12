<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->nullable();
            $table->integer('amount')->nullable();
            $table->string('ref_no', 100)->nullable();
            $table->string('method', 100)->default('cash')->nullable();
            $table->unsignedInteger('my_course_id')->nullable();
            $table->string('description')->nullable();
            $table->string('year')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
