<?php

use Illuminate\Database\Migrations\Migration;

class CreatingDays extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('days', function($table)
		{
			$table->increments('id');
			$table->string('name');

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
		Schema::drop('days');
	}

}