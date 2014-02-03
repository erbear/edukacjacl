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
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->string('code');
			$table->integer('kind_id')->unsigned();
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