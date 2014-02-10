<?php

use Illuminate\Database\Migrations\Migration;

class CreatecodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('codes', function($table)
		{
			$table->increments('id')->unsigned();
	        $table->string('name')->unique();
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
		Schema::drop('codes');
	}

}