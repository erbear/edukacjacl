<?php

use Illuminate\Database\Migrations\Migration;

class CreateLectureUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lecture_user', function($table){
			$table->increments('id');
			$table->string('lecture_id');
			$table->string('user_id');
			$table->string('semestr');
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
		Schema::drop('lecture_user');
	}

}