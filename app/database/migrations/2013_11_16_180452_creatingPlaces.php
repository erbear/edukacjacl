<?php

use Illuminate\Database\Migrations\Migration;

class CreatingPlaces extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('places', function($table){
			$table->increments('id')->unsigned();
			$table->string('room');
			$table->string('building');
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
		Schema::drop('places');
	}

}