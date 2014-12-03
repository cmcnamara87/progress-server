<?php

class UsersController extends \BaseController {

	public function login()
	{
		// http://scotch.io/tutorials/simple-and-easy-laravel-login-authentication
		$request = Request::instance();
		$data = (array)json_decode($request->getContent());
		if(!$data) {
			$data = Input::all();
		}
		if(!isset($data['password'])) {
			$data['password'] = '';
		}
		$data['password'] = '';
		if (!Auth::attempt($data, true)) {
			App::abort(400, 'Incorrect email or password.');
		}
        return Response::json(Auth::user());
	}

	public function logout()
	{
		Auth::logout();
	}

	public function register() {
		$request = Request::instance();
		$data = (array)json_decode($request->getContent());
		if(!$data) {
			$data = Input::all();
		}
        $newUser = User::create($data);

        // Make the user follow everyone
		$users = User::all();
		foreach($users as $user) {
			if($user->id === $newUser->id) {
				continue;
			}
			$newUser->follows()->save($user);
			$user->follows()->save($newUser);
		}
        return Response::json($newUser);
	}

	/**
	 * Display a listing of users
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = User::all();
		return Response::json($users);
	}

	/**
	 * Store a newly created user in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), User::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$user = User::create($data);
		return Response::json($user);
	}

	/**
	 * Display the specified user.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$user = User::findOrFail($id);
		if(Auth::check()) {
			// Set if they follow
			$user->isFollowing = Auth::user()->getIsFollowing($user->id);
		}
		return Response::json($user);
	}

	/**
	 * Update the specified user in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$user = User::findOrFail($id);

		$validator = Validator::make($data = Input::all(), User::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$user->update($data);
		return Response::json($user);
	}

	/**
	 * Remove the specified user from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		User::destroy($id);
		return Response::json($id);
	}

}
