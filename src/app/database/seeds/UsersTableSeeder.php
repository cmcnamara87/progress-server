<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder {

	public function run()
	{
        DB::table('users')->truncate();

        include 'progress-db.php';

        foreach($user as $existingUser) {
            $newUser = new User;
            $newUser->id = $existingUser['id'];
            $newUser->username = str_replace(' ', '_', strtolower($existingUser['name']));
            if(!$existingUser['name']) {
              continue;
            }
            $newUser->name = $existingUser['name'];
            
            if(!$existingUser['email']) {
              continue;
            }
            $newUser->email = $existingUser['email'];
            $newUser->password = Hash::make('');
            if(!$existingUser['picture']) {
              continue;
            }
            $newUser->picture = $existingUser['picture'];
            $newUser->save();
        }
  //       User::create([
  //           'name' => 'Craig McNamara',
  //           'username' => 'cmcnamara87',
  //           'email' => 'cmcnamara87@gmail.com',
  //           'password' => Hash::make('test'),
  //           'picture' => 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpf1/v/t1.0-1/p200x200/10616375_10152367709402615_5025993933117187205_n.jpg?oh=98c3455e98f4d54db11d04c1cefd87b5&oe=54A7DD93&__gda__=1418982123_3a5e933b1ca129dbdc14c71d2661676f'
  //       ]);

		// $faker = Faker::create();

		// foreach(range(1, 10) as $index)
		// {
  //           $user = new User;
  //           $user->name = $faker->name;
  //           $user->username = $faker->username;
  //           $user->email = $faker->email;
  //           $user->password = Hash::make('test');
  //           $user->picture = 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpf1/v/t1.0-1/p200x200/10616375_10152367709402615_5025993933117187205_n.jpg?oh=98c3455e98f4d54db11d04c1cefd87b5&oe=54A7DD93&__gda__=1418982123_3a5e933b1ca129dbdc14c71d2661676f';
  //           $user->save();
		// }
	}

}