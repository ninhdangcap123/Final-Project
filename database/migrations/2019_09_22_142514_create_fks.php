<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFks extends Migration
{

    public function up()
    {
        Schema::table('lgas', function (Blueprint $table) {
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('lga_id')->references('id')->on('lgas')->onDelete('set null');
            $table->foreign('bg_id')->references('id')->on('blood_groups')->onDelete('set null');
            $table->foreign('nal_id')->references('id')->on('nationalities')->onDelete('set null');
        });

        Schema::table('my_courses', function (Blueprint $table) {
            $table->foreign('major_id')->references('id')->on('majors')->onDelete('set null');
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->foreign('my_course_id')->references('id')->on('my_courses')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('users');
            $table->foreign('my_course_id')->references('id')->on('my_courses')->onDelete('cascade');
        });

        Schema::table('student_records', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('my_course_id')->references('id')->on('my_courses')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('my_parent_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('dorm_id')->references('id')->on('dorms')->onDelete('set null');
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('student_records')->onDelete('cascade');
            $table->foreign('my_course_id')->references('id')->on('my_courses')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('set null');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->foreign('major_id')->references('id')->on('majors')->onDelete('cascade');
        });

        Schema::table('pins', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('exam_records', function (Blueprint $table) {
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('my_course_id')->references('id')->on('my_courses')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });

        Schema::table('staff_records', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('my_course_id')->references('id')->on('my_courses')->onDelete('cascade');
        });

        Schema::table('payment_records', function (Blueprint $table) {
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->foreign('pr_id')->references('id')->on('payment_records')->onDelete('cascade');
        });

        Schema::table('time_table_records', function (Blueprint $table) {
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('my_course_id')->references('id')->on('my_courses')->onDelete('cascade');
        });

        Schema::table('time_slots', function (Blueprint $table) {
            $table->foreign('ttr_id')->references('id')->on('time_table_records')->onDelete('cascade');
        });

        Schema::table('time_tables', function (Blueprint $table) {
            $table->foreign('ttr_id')->references('id')->on('time_table_records')->onDelete('cascade');
            $table->foreign('ts_id')->references('id')->on('time_slots')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });

    }

    public function down()
    {

    }
}
