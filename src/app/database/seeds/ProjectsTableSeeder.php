<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ProjectsTableSeeder extends Seeder {

	public function run()
	{
        DB::table('projects')->truncate();

        include 'progress-db.php';

        foreach($project as $existingProject) {
            $newProject = new Project;
            $newProject->id = $existingProject['id'];
            if($existingProject['name']) {
                $newProject->name = $existingProject['name'];
            } else {
                $newProject->name = '';
            }
            if($existingProject['user_id']) {
                $newProject->user_id = $existingProject['user_id'];
            } else {
                $newProject->user_id = 0;
            }
            $newProject->text = '';
            $newProject->save();
        }
	}

}