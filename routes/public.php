<?php

$app->post('/hello', function() use ($app) {
	// echo '{"test_thing": "go now 2222"}';
	echo "NO JSON";
});

$app->get('/setup', function() {
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

$app->get('/setuproject/:projectId', function($projectId) {
	$project = R::load('project', $projectId);
		// foreach($projects as $project) {
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
		R::store($project);
	// }
});

$app->get('/fixkhoa', function() {
	$projectId = 62;
	$startOfWeek = strtotime('Monday this week');
	$time = R::findOne('time', ' type = \'week\' AND project_id = :projectId AND date = :startOfWeek ', array(
		':projectId' => $projectId,
		':startOfWeek' => $startOfWeek
	));

	$progresses = R::find('progress', ' created > :startOfWeek AND project_id = :projectId ', array(
		':startOfWeek' => $startOfWeek,
		':projectId' => $projectId,
	));

	$previousTime = null;
	$time->seconds = 0;
	foreach($progresses as $progress) {
		$hasAlreadyMadeProgress = !!$previousTime;
		if($hasAlreadyMadeProgress) {
			$hasWorkedWithinAnHour = $previousTime > ($progress->created - PROGRESS_ACTIVE_TIME_MINUTES * 60);
			if($hasWorkedWithinAnHour) {
				$time->seconds += min($progress->created - $previousTime, PROGRESS_MAX_AMOUNT_MINUTES * 60);	
			} else {
				$time->seconds += PROGRESS_DEFAULT_AMOUNT_MINUTES;
			}
		}
		$previousTime = $progress->created;
	}
	$time->time = gmdate("z\d G\h i\m s\s", $time->seconds);
	R::store($time);
});

$app->get('/createleaderboard', function() {
	$startOfWeek = strtotime('Monday this week');

	$users = R::findAll('user');
	foreach($users as $user) {
		// $user = R::load('user', $userId);
		$projects = $user->ownProjectList;

		foreach($projects as $project) {
			// $time = 0;
			$previousTime = null;
			$project->seconds = 0;

			$time = R::dispense('time');
			$time->type = 'week';
			$time->date = $startOfWeek;
			$time->time = 0;
			$time->project = $project;
			$time->user = $user;

			$progresses = R::find('progress', ' created > :startOfWeek AND project_id = :projectId ', array(
				':startOfWeek' => $startOfWeek,
				':projectId' => $project->id,
			));

			if(count($progresses) == 0) {
				continue;
			}
		
			foreach($progresses as $progress) {
				$hasAlreadyMadeProgress = !!$previousTime;
				if($hasAlreadyMadeProgress) {
					$hasWorkedWithinAnHour = $previousTime > ($progress->created - PROGRESS_ACTIVE_TIME_MINUTES * 60);
					if($hasWorkedWithinAnHour) {
						$time->seconds += min($progress->created - $previousTime, PROGRESS_MAX_AMOUNT_MINUTES * 60);	
					} else {
						$time->seconds += PROGRESS_DEFAULT_AMOUNT_MINUTES;
					}
				}
				$previousTime = $progress->created;
			}
			$time->time = gmdate("z\d G\h i\m s\s", $time->seconds);

			R::store($time);
		}
	}
	
});

$app->get('/me/following/posts', function() {
	$posts = R::findAll('post', ' ORDER BY created DESC LIMIT 20');
	foreach($posts as &$post) {
		$post->user = R::load('user', $post->user_id);
		$post->project = R::load('project', $post->project_id);
		$post->collection = R::load('collection', $post->collection_id);
		$post->collection->ownFileList;
		$post->ownLikeList;
		foreach($post->ownLikeList as $like) {
			$like->user;
		}
		$post->ownCommentList;
		foreach($post->ownCommentList as $comment) {
			$comment->user;
		}
	}
	echo json_encode(R::exportAll($posts), JSON_NUMERIC_CHECK);
});
$app->get('/me/following', function() {
	$users = R::findAll('user');
	$export = array_map(function($user) {
		$result = new stdClass();
		$result->id = $user->id;
		$result->name = $user->name;
		return $result;
	}, $users);
	echo json_encode(array_values($export), JSON_NUMERIC_CHECK);
});

$app->get('/me/following/online', function() {

	if(isset($_SESSION['userId'])) {
		$user = R::load('user', $_SESSION['userId']);
		$user->lastseen = time();
		$user->lastseentext = date("D M d, Y G:i");
		R::store($user);
	}

	$users = R::findAll('user');
	$online = array();
	foreach($users as $user) {
		// Get the last project
		$lastProgress = R::findOne('progress', ' user_id = :user_id ORDER BY created DESC LIMIT 1 ', array(':user_id' => $user->id));	

		if($lastProgress && $lastProgress->created + 60*60 > time()) {	

			$user = $user->export(false, false, true);
			$user['activeProject'] = R::load('project', $lastProgress->project_id)->export(false, false, true);
			$user['state'] = 'idle';
			$user['lastProgress'] = $lastProgress->export(false, false, true);
			if($lastProgress->created + 15*60 > time()) {			
				$user['state'] = 'active';
			}
			$online[] = $user;
		}
	}
	echo json_encode($online, JSON_NUMERIC_CHECK);
});

$app->get('/users/:userId', function($userId) {
	$user = R::load('user', $userId);
	$export = $user->export(false, false, true);
	unset($export['password']);
	unset($export['email']);
	echo json_encode($export);
});

$app->get('/users/:userId/posts', function($userId) {
	$user = R::load('user', $userId);
	$posts = R::find('post', ' user_id = :user_id ORDER BY created DESC ', array('user_id' => $userId));
	echo json_encode(R::exportAll($posts));
});
$app->get('/posts/:postId', function($postId) {
	$post = R::load('post', $postId);
	$post->user;
	$post->project;
	$post->ownLike;
	if($post->collection) {
		$post->collection->ownFileList;
	}
	foreach($post->ownLike as $like) {
		$like->user;
	}
	foreach($post->ownComment as $comment) {
		$comment->user;
	}
	echo json_encode($post->export(), JSON_NUMERIC_CHECK);
});

$app->get('/users/:userId/projects', function($userId) {
	$user = R::load('user', $userId);
	$projects = $user->ownProjectList;
	// header( 'Content-Type: text/html' );
	// foreach($projects as $project) {
	// 	// $time = 0;
	// 	$previousTime = null;
	// 	$project->seconds = 0;
	// 	foreach($project->ownProgressList as $progress) {
	// 		$hasAlreadyMadeProgress = !!$previousTime;
	// 		if($hasAlreadyMadeProgress) {
	// 			$hasWorkedWithinAnHour = $previousTime > ($progress->created - PROGRESS_ACTIVE_TIME_MINUTES * 60);
	// 			if($hasWorkedWithinAnHour) {
	// 				$project->seconds += min($progress->created - $previousTime, PROGRESS_MAX_AMOUNT_MINUTES * 60);	
	// 			} else {
	// 				$project->seconds += PROGRESS_DEFAULT_AMOUNT_MINUTES;
	// 			}
	// 		}
	// 		$previousTime = $progress->created;
	// 	}
	// 	$project->time = gmdate("z\d G\h i\m s\s", $project->seconds);
	// 	R::store($project);
	// }
	

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

	// $previousTime = null;
	// $project->seconds = 0;
	// foreach($project->ownProgressList as $progress) {
	// 	$hasAlreadyMadeProgress = isset($previousTime);
	// 	if($previousTime) {
	// 		$hasWorkedWithinAnHour = $previousTime > ($progress->created - PROGRESS_ACTIVE_TIME_MINUTES * 60);
	// 		if($hasWorkedWithinAnHour) {
	// 			$project->seconds += min($progress->created - $previousTime, PROGRESS_MAX_AMOUNT_MINUTES * 60);	
	// 		} else {
	// 			$project->seconds += PROGRESS_DEFAULT_AMOUNT_MINUTES;
	// 		}
	// 	}
	// 	$previousTime = $progress->created;
	// }
	// $project->time = gmdate("z\d G\h i\m s\s", $project->seconds);
	$project->ownDirectoryList;
	$blah = $project->export(false, false, true);
	unset($blah['ownProgressList']);
	unset($blah['ownProgress']);
	unset($blah['progress']);
	$blah['directories'] = R::exportAll($project->ownDirectoryList);
	// echo "<pre>";
	// print_r($blah);
	// echo "</pre>";
	// die();
	echo json_encode($blah);
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
