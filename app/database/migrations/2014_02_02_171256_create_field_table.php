<?php

use Illuminate\Database\Migrations\Migration;

class CreateFieldTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fields', function($table)
		{
			$table->increments('id')->unsigned();
	        $table->string('name');
	        $table->integer('semestr')->unsigned();
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
		Schema::drop('fields');
	}

}