<?php

use Illuminate\Database\Migrations\Migration;

class CreateTermTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('terms', function($table)
		{
			$table->increments('id')->unsigned();
	        $table->integer('code_id')->unsigned();
	        $table->integer('day_id')->unsigned();
	        $table->integer('hour_id')->unsigned();
	        $table->integer('place_id')->unsigned();
	        $table->integer('space_id')->unsigned();
	        $table->integer('teacher_id')->unsigned();
	        $table->string('week');
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
		Schema::drop('terms');
	}
	

}