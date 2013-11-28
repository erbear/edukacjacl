<?php

use Illuminate\Database\Migrations\Migration;

class AddWeekToLectures extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('lectures', function($table)
			{
				$table->string('week');
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
		Schema::table('lectures', function($table)
		{
    		$table->dropColumn('week');
    		
		});
	}
		

}