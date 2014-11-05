<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		if (App::environment() === 'production') {
            exit('I just stopped you getting fired. Love Phil');
        }

		Eloquent::unguard();

		$this->call('UsersTableSeeder');
		$this->command->info('User table seeded.');

		$this->call('ProjectsTableSeeder');
		$this->command->info('Project table seeded.');

		$this->call('PostsTableSeeder');
		$this->command->info('Post table seeded.');

		$this->call('NotificationsTableSeeder');
		$this->command->info('Notification table seeded.');

		$this->call('WatchesTableSeeder');
		$this->command->info('Watches table seeded.');

		$this->call('TimersTableSeeder');
		$this->command->info('Timers table seeded.');

	}

}
