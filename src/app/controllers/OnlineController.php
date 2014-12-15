<?php

use Illuminate\Database\Eloquent\Collection;

class OnlineController extends \BaseController {

	public function online() {
	
		// Deal with the mac app dying!
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($userAgent, 'Progress') !== false) {
    		$users = User::all();

			$c1 = new Collection([ Auth::user() ]);
			unset($c1[0]->follows);
			$users = $users->merge($c1);
			
			$online = array();
			foreach($users as $user) {
				$activeProject = $user->getActiveProject();
				if($activeProject) {
					$user->activeProject = $activeProject;
					$user->lastProgress = $user->getLastProgress();
					$user->lastProgress->created = $user->lastProgress->created_at->timestamp;
					$online[] = $user;
				}
			}
			return Response::json($online);
		}

		// For the website
		$follows = Auth::user()->follows;
		$onlines = array();

		foreach($follows as $follow) {
			$project = $follow->project;
			if($project->getIsActive()) {
				$online = new stdClass();
				$online->project = $project;
				$online->users = $project->getActiveUsers();
				$onlines[] = $online;
			}
		}
		return Response::json($onlines);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
