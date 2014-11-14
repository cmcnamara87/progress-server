<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserFollowsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_follows', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
      		$table->integer('followee_id');
			$table->timestamps();
		});

		// Add follow relationships for everyone
		$users = User::all();
		foreach($users as $follower) {
			foreach($users as $followee) {
				if($follower->id === $followee->id) {
					continue;
				}
				$follower->follows()->save($followee);
			}
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_follows');
	}

}
