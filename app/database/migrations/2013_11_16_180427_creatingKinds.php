<?php

use Illuminate\Database\Migrations\Migration;

class CreatingKinds extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('kinds', function($table){
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
		Schema::drop('kinds');
	}

}