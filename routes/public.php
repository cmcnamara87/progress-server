<?php

$app->get('/hello', function() use ($app) {
	echo '{"test_thing": "go now 2222"}';
});

$app->get('/setup', function() {
	R::nuke();

	// $user = R::dispense('user');
	// $user->email = 'test@example.com';
	// $user->name = 'Test';
	// $user->password = md5('test');
	// R::store($user);

	$user = R::dispense('user');
	$user->email = 'cmcnamara87@gmail.com';
	$user->name = 'Craig';
	$user->password = md5('test');
	R::store($user);

});

$app->get('/users/:userId/projects', function($userId) {
	$user = R::load('user', $userId);
	$projects = $user->ownProjectList;
	foreach($projects as $project) {
		// $time = 0;
		$previousTime = null;
		$project->seconds = 0;
		foreach($project->ownProgressList as $progress) {
			if($previousTime) {
				$project->seconds += min($progress->created - $previousTime, 5 * 60);
			}
			$previousTime = $progress->created;
		}
		$project->time = gmdate("H:i:s", $project->seconds);
	}

	$export = array_map(function($project) {
		$result = new stdClass();
		$result->id = $project->id;
		$result->name = $project->name;
		$result->time = $project->time;
		$result->seconds = $project->seconds;
		$result->directories = R::exportAll($project->ownDirectoryList);
		return $result;
	}, $projects);

	echo json_encode(array_values($export), JSON_NUMERIC_CHECK);
});