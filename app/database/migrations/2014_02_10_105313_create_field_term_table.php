<?php

use Illuminate\Database\Migrations\Migration;

class CreateFieldTermTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('field_term', function($table)
		{
			$table->increments('id')->unsigned();
	        $table->integer('field_id')->unsigned();
	        $table->integer('term_id')->unsigned();
	        $table->integer('semestr')->unsigned();
	        $table->integer('year')->unsigned();
	        $table->timestamps();
	        $table->unique(array('field_id', 'term_id', 'semestr', 'year'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	
		Schema::drop('field_term');
	
	}

}