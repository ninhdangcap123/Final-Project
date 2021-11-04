<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_table_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->unsignedInteger('my_course_id')->nullable();
            $table->unsignedInteger('exam_id')->nullable();
            $table->string('year', 100)->nullable();
            $table->timestamps();


        });

        Schema::create('time_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ttr_id')->nullable();
            $table->tinyInteger('hour_from')->nullable();
            $table->string('min_from', 2)->nullable();
            $table->string('meridian_from', 2)->nullable();
            $table->tinyInteger('hour_to')->nullable();
            $table->string('min_to', 2)->nullable();
            $table->string('meridian_to', 2)->nullable();
            $table->string('time_from', 100)->nullable();
            $table->string('time_to', 100)->nullable();
            $table->string('timestamp_from', 50)->nullable();
            $table->string('timestamp_to', 50)->nullable();
            $table->string('full', 100)->nullable();
            $table->timestamps();



        });

        Schema::create('time_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ttr_id')->nullable();
            $table->unsignedInteger('ts_id')->nullable();
            $table->unsignedInteger('subject_id')->nullable();
            $table->string('exam_date', 50)->nullable();
            $table->string('timestamp_from', 100)->nullable();
            $table->string('timestamp_to', 100)->nullable();
            $table->string('day', 50)->nullable();
            $table->tinyInteger('day_num')->nullable();
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
        Schema::dropIfExists('time_tables');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('time_table_records');
    }
}
