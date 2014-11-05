<?php

class MeProjectsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$projects = Auth::user()->projects;
		$projects->load('watches');
		foreach($projects as $project) {
			$project->directories = $project->watches;	
			unset($project->watches);
		}
		return Response::json($projects);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Project::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
		// overwrite the user id
		$data['user_id'] = Auth::user()->id;
		$project = Project::create($data);
		return Response::json($project);
		// return Redirect::route('projects.index');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$project = Project::findForMeOrFail($id);
		return Response::json($project);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$project = Project::findForMeOrFail($id);

		$validator = Validator::make($data = Input::all(), Project::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$project->update($data);
		return Response::json($project);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$project = Project::findForMeOrFail($id);
		Project::destroy($id);
		return Response::json($id);
	}


}
