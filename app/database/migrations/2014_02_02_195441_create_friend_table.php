<?php

use Illuminate\Database\Migrations\Migration;

class CreateFriendTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('friends', function($table)
		{
			$table->increments('id')->unsigned();
	        $table->integer('user_id')->unsigned();
	        $table->integer('f_user_id')->unsigned();
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
		Schema::drop('friends');
	}

}