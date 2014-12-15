<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFollowsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('follows', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('project_id');
			$table->integer('status'); // 0 requested, 1 accepted
			$table->timestamps();
		});

		// Add follow relationships for everyone
		$users = User::all();
		$projects = Project::all();
		foreach($users as $user) {
			foreach($projects as $project) {
				$follow = new Follow();
				$follow->user_id = $user->id;
				$follow->project_id = $project->id;
				$follow->status = 1;
				$follow->save();
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
		Schema::drop('follows');
	}

}
