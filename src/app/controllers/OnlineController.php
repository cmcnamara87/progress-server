<?php

class OnlineController extends \BaseController {

	public function online() {
		$users = User::all();
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
