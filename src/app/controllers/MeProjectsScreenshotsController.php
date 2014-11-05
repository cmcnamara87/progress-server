<?php

class MeProjectsScreenshotsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($projectId)
	{
		echo 'hello world';
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($projectId)
	{
	    if (!Input::hasFile('file')) {
		    App::abort(400, 'No file uploaded');
		}
		
		$request = Request::instance();
		$data = (array)json_decode($request->getContent());
		if(!$data) {
			$data = Input::all();
		}

		if(!isset($data['project_id'])) {
			$activeProject = Auth::user()->getActiveProject();
			if(!$activeProject) {
				App::abort('400', 'No project id and no active project');
			}
			$data['project_id'] = $activeProject->id;
		}
		$post = Post::createPicturePost($data['text'], Auth::user()->id, $data['project_id'], Input::file('file'));
		$post->load('media');
		return Response::json($post);
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
