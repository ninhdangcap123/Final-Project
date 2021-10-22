<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 40);
            $table->unsignedInteger('major_id')->nullable();
            $table->tinyInteger('mark_from')->nullable();
            $table->tinyInteger('mark_to')->nullable();
            $table->string('remark', 40)->nullable();
            $table->timestamps();
        });

//        Schema::table('grades', function (Blueprint $table) {
//            $table->unique(['name', 'major_id', 'remark']);
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grades');
    }
}
