<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DropTimerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('timer');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('timer', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('seconds');
			$table->string('type', 32);
			$table->unsignedInteger('project_id');

			$table->timestamps();
		});
	}

}
