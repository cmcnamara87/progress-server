<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class NotificationsTableSeeder extends Seeder {

	public function run()
	{
        DB::table('notifications')->truncate();

        include 'progress-db.php';

        foreach($notification as $existingNotification) {
            // array('id' => '1','text' => 'Craig McNamara liked on your post.','isread' => '1','user_id' => '1','post_id' => '862'),
            $newNotification = new Notification;
            $newNotification->text = $existingNotification['text'];
            $newNotification->isread = $existingNotification['isread'];
            $newNotification->user_id = $existingNotification['user_id'];
            if(!$existingNotification['post_id']) {
                continue;
            }
            $newNotification->post_id = $existingNotification['post_id'];
            $newNotification->save();
        }
	}

}