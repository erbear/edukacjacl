<?php

use Illuminate\Database\Migrations\Migration;

class AddingTimeStamps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('days', function($table)
			{
				$table->timestamps();
			});
		Schema::table('hours', function($table)
			{
				$table->timestamps();
			});
		Schema::table('kinds', function($table)
			{
				$table->timestamps();
			});
		Schema::table('places', function($table)
			{
				$table->timestamps();
			});
		Schema::table('teachers', function($table)
			{
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
		Schema::table('days', function($table)
		{
    		$table->dropColumn('created_at');
    		$table->dropColumn('updated_at');
		});
		Schema::table('hours', function($table)
		{
    		$table->dropColumn('created_at');
    		$table->dropColumn('updated_at');
		});
		Schema::table('kinds', function($table)
		{
    		$table->dropColumn('created_at');
    		$table->dropColumn('updated_at');
		});
		Schema::table('places', function($table)
		{
    		$table->dropColumn('created_at');
    		$table->dropColumn('updated_at');
		});
		Schema::table('teachers', function($table)
		{
    		$table->dropColumn('created_at');
    		$table->dropColumn('updated_at');
		});
		
	}

}