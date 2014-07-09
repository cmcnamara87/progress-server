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

$app->get('/me/following/posts', function() {
	$posts = R::findAll('post', ' ORDER BY created DESC ');
	foreach($posts as &$post) {
		$post->user = R::load('user', $post->user_id);
		$post->project = R::load('project', $post->project_id);
		$post->collection = R::load('collection', $post->collection_id);
		$post->collection->ownFileList;
	}
	echo json_encode(R::exportAll($posts));
});
$app->get('/me/following', function() {
	$users = R::findAll('user');
	echo json_encode(R::exportAll($users));
});

$app->get('/me/following/online', function() {
	$users = R::findAll('user');
	$online = array();
	foreach($users as $user) {
		// Get the last project
		$lastProgress = R::findOne('progress', ' user_id = :user_id ORDER BY created DESC ', array(':user_id' => $user->id));	

		if($lastProgress && $lastProgress->created + 60*60 > time()) {	

			$user->activeProject = R::load('project', $lastProgress->project_id)->export();
			$user->state = 'idle';
			$user->lastProgress = $lastProgress->export(false, false, true);
			if($lastProgress->created + 15*60 > time()) {			
				$user->state = 'active';
			}
			$online[] = $user->export(false, false, true);;
		}
	}
	echo json_encode($online);
});

$app->get('/users/:userId/posts', function($userId) {
	$user = R::load('user', $userId);
	$posts = R::find('post', ' user_id = :user_id ORDER BY created DESC ', array('user_id' => $userId));
	echo json_encode(R::exportAll($posts));
});

$app->get('/users/:userId/projects', function($userId) {
	$user = R::load('user', $userId);
	$projects = $user->ownProjectList;
	header( 'Content-Type: text/html' );
	foreach($projects as $project) {
		// $time = 0;
		$previousTime = null;
		$project->seconds = 0;
		foreach($project->ownProgressList as $progress) {
			$hasAlreadyMadeProgress = !!$previousTime;
			if($hasAlreadyMadeProgress) {
				$hasWorkedWithinAnHour = $previousTime > ($progress->created - PROGRESS_ACTIVE_TIME_MINUTES * 60);
				if($hasWorkedWithinAnHour) {
					$project->seconds += min($progress->created - $previousTime, PROGRESS_MAX_AMOUNT_MINUTES * 60);	
				} else {
					$project->seconds += PROGRESS_DEFAULT_AMOUNT_MINUTES;
				}
			}
			$previousTime = $progress->created;
		}
		$project->time = gmdate("z\d G\h i\m s\s", $project->seconds);
	}
	

	$export = array_map(function($project) {
		$result = new stdClass();
		$result->id = $project->id;
		$result->name = $project->name;
		$result->time = $project->time;
		$result->seconds = $project->seconds;
		$result->directories = R::exportAll($project->ownDirectoryList);
		$result->user = R::load('user', $project->user_id)->export();
		return $result;
	}, $projects);

	echo json_encode(array_values($export), JSON_NUMERIC_CHECK);
});

$app->get('/users/:userId/projects/:projectId', function($userId, $projectId) {
	$project = R::load('project', $projectId);
	$project->user = R::load('user', $userId);

	$previousTime = null;
	$project->seconds = 0;
	foreach($project->ownProgressList as $progress) {
		$hasAlreadyMadeProgress = isset($previousTime);
		if($previousTime) {
			$hasWorkedWithinAnHour = $previousTime > ($progress->created - PROGRESS_ACTIVE_TIME_MINUTES * 60);
			if($hasWorkedWithinAnHour) {
				$project->seconds += min($progress->created - $previousTime, PROGRESS_MAX_AMOUNT_MINUTES * 60);	
			} else {
				$project->seconds += PROGRESS_DEFAULT_AMOUNT_MINUTES;
			}
		}
		$previousTime = $progress->created;
	}
	$project->time = gmdate("z\d G\h i\m s\s", $project->seconds);
	echo json_encode($project->export(false, false, true));
});

$app->get('/users/:userId/projects/:projectId/posts', function($userId, $projectId) {
	$user = R::load('user', $userId);
	$posts = R::find('post', ' user_id = :user_id AND project_id = :project_id ORDER BY created DESC ', array('user_id' => $userId, 'project_id' => $projectId));
	// $posts = R::exportAll($posts);
	foreach($posts as &$post) {
		$post->user = R::load('user', $post->user_id);
		$post->project = R::load('project', $post->project_id);
		$post->collection = R::load('collection', $post->collection_id);
		$post->collection->ownFileList;
	}
	echo json_encode(R::exportAll($posts));
});
