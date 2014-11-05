<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DropDirectoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('directories');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('directories', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('path', 340);
			$table->unsignedInteger('project_id');

			$table->timestamps();
		});
	}

}
