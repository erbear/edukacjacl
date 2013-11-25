<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		$day = new Day();
		$day->name = "dzien1";
		$day->save();
		$hour= new Hour();
		$hour->start = "11:30";
		$hour->finish = "12:30";
		$hour->save();
		$kind = new Kind();
		$kind->name = "kind1";
		$kind->save();
		$place = new Place();
		$place->building = "building1";
		$place->room = "room1";
		$place->save();
		$teacher = new Teacher();
		$teacher->name = "teacher1";
		$teacher->save();
		$lecture = new Lecture();
		$lecture->name = "lecture1";
		$lecture->day_id = 1;
		$lecture->hour_id = 1;
		$lecture->kind_id = 1;
		$lecture->place_id = 1;
		$lecture->teacher_id = 1;
		$lecture->save();

		$day = new Day();
		$day->name = "dzien2";
		$day->save();
		$hour= new Hour();
		$hour->start = "11:00";
		$hour->finish = "12:00";
		$hour->save();
		$kind = new Kind();
		$kind->name = "kind2";
		$kind->save();
		$place = new Place();
		$place->building = "building2";
		$place->room = "room2";
		$place->save();
		$teacher = new Teacher();
		$teacher->name = "teacher2";
		$teacher->save();
		$lecture = new Lecture();
		$lecture->name = "lecture2";
		$lecture->day_id = 2;
		$lecture->hour_id = 2;
		$lecture->kind_id = 2;
		$lecture->place_id = 2;
		$lecture->teacher_id = 2;
		$lecture->save();


		// $this->call('UserTableSeeder');
	}

}