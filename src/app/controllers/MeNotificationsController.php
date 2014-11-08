<?php

class MeNotificationsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$notifications = Notification::findUnreadForUser(Auth::user());

		if($notifications->count() < 5) {
			$readNotifications = Auth::user()->notifications()->read()->orderBy('created_at', 'desc')->take(5 - $notifications->count())->get();
			$notifications = $notifications->merge($readNotifications);
		}
		return Response::json($notifications, 200, [], JSON_NUMERIC_CHECK);
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
		$request = Request::instance();
		$data = (array)json_decode($request->getContent());

		$notification = Notification::find($id);
		$notification->isread = $data['isread'];
		$notification->save();
		return Response::json($notification);
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
