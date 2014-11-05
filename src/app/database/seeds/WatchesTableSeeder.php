<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class WatchesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('watches')->truncate();

        include 'progress-db.php';

        foreach($directory as $existingDirectory) {
        // array('id' => '1','path' => '/Users/cmcnamara87/Sites/progress','project_id' => '1'),
            $newWatch = new Watch;
            $newWatch->id = $existingDirectory['id'];
            $newWatch->path = $existingDirectory['path'];
            if(!$existingDirectory['project_id']) {
                continue;
            }
            $newWatch->project_id = $existingDirectory['project_id'];
            $newWatch->save();
        }
    }
}
