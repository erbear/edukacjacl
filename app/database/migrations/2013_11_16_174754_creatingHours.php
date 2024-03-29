<?php

use Illuminate\Database\Migrations\Migration;

class CreatingHours extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('hours', function($table){
			$table->increments('id')->unsigned();
			$table->time('start');
			$table->time('finish');
			$table->unique(array('start', 'finish'));
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
		Schema::drop('hours');
	}

}