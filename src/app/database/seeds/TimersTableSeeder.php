<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Carbon\Carbon;

class TimersTableSeeder extends Seeder {

	public function run()
	{
		DB::table('timers')->truncate();

        include 'progress-db.php';

        foreach($time as $existingTime) {
            $newTimer = new Timer;
            if(!$existingTime['seconds']) {
                continue;
            }
            if(!$existingTime['project_id']) {
                continue;
            }
            $newTimer->seconds = $existingTime['seconds'];
            $newTimer->type = $existingTime['type'];
            $newTimer->starting = Carbon::createFromTimeStamp($existingTime['date']);
            $newTimer->project_id = $existingTime['project_id'];
            $newTimer->save();
        }
	}

}