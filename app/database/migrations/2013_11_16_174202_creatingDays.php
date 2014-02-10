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
			$table->increments('id')->unsigned();
			$table->string('name')->unique();

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