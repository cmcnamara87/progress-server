<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddProjectIdToProgressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('progress', function(Blueprint $table)
		{
			$table->unsignedInteger('project_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('progress', function(Blueprint $table)
		{
			$table->dropColumn('project_id');	
		});
	}

}
