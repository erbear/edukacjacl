<?php

use Illuminate\Database\Migrations\Migration;

class AddSemestrToLectureTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('lectures', function($table)
			{
				$table->string('semestr');
			});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('lectures', function($table)
		{
    		$table->dropColumn('semestr');
		});
	}

}