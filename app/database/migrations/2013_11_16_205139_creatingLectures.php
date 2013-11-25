<?php

use Illuminate\Database\Migrations\Migration;

class CreatingLectures extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('lectures', function($table){
			$table->increments('id');
			$table->string('name');
			$table->integer('day_id');
			$table->integer('hour_id');
			$table->integer('kind_id');
			$table->integer('place_id');
			$table->integer('teacher_id');
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
		//
		Schema::drop('lectures');
	}

}