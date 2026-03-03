<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('course_id');
            $table->integer('points')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('bad_answers')->default(0);
            $table->integer('time')->default(0);
            $table->integer('level')->default(1);
            $table->boolean('finish')->default(0);

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
