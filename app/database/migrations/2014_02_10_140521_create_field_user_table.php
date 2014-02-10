<?php

use Illuminate\Database\Migrations\Migration;

class CreateFieldUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('field_user', function($table)
		{
			$table->increments('id')->unsigned();
	        $table->integer('field_id')->unsigned();
	        $table->integer('user_id')->unsigned();
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
		Schema::drop('field_user');
	}

}