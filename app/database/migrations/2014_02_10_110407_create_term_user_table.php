<?php

use Illuminate\Database\Migrations\Migration;

class CreateTermUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('term_user', function($table)
		{
			$table->increments('id')->unsigned();
	        $table->integer('term_id')->unsigned();
	        $table->integer('user_id')->unsigned();
	        $table->boolean('joined');
	        $table->timestamps();
	        $table->unique(array('user_id', 'term_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{	
		Schema::drop('term_user');
	}

}