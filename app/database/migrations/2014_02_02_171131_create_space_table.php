<?php

use Illuminate\Database\Migrations\Migration;

class CreateSpaceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spaces', function($table)
		{
			$table->increments('id')->unsigned();
	        $table->integer('taken')->unsigned();
	        $table->integer('all')->unsigned();
	        $table->unique(array('taken', 'all'));
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
		Schema::drop('profiles');
	}

}