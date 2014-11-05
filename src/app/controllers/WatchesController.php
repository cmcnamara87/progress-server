<?php

class WatchesController extends \BaseController {

	/**
	 * Display a listing of watches
	 *
	 * @return Response
	 */
	public function index()
	{
		$watches = Watch::all();

		return View::make('watches.index', compact('watches'));
	}

	/**
	 * Show the form for creating a new watch
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('watches.create');
	}

	/**
	 * Store a newly created watch in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Watch::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Watch::create($data);

		return Redirect::route('watches.index');
	}

	/**
	 * Display the specified watch.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$watch = Watch::findOrFail($id);

		return View::make('watches.show', compact('watch'));
	}

	/**
	 * Show the form for editing the specified watch.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$watch = Watch::find($id);

		return View::make('watches.edit', compact('watch'));
	}

	/**
	 * Update the specified watch in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$watch = Watch::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Watch::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$watch->update($data);

		return Redirect::route('watches.index');
	}

	/**
	 * Remove the specified watch from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Watch::destroy($id);

		return Redirect::route('watches.index');
	}

}
