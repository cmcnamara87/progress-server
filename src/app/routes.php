<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


// $currentUser = User::all()->first();


Route::get('/', function () {
    return View::make('hello');
});


Route::get('foo/bar', function()
{
    return 'Hello World';
});


Route::resource('users', 'UsersController');

// From http://code.tutsplus.com/tutorials/laravel-4-a-start-at-a-restful-api-updated--net-29785
// Route::group(array('prefix' => 'api/v2', 'before' => 'auth.basic'), function()
// Route::group(array('prefix' => '', 'before' => 'auth.basic'), function()
// {
    
// });
// 

/*

/me/projects
/me/projects/1
/me/projects/1/watches
/me/projects/1/progress
/me/projects/1/screenshots

/me/following
/me/following/posts
 */

// 
Route::group(array('prefix' => ''), function()
{
    // Session
    Route::get('session/user', function()
    {
        
        // var_dump(Auth::viaRemember());
        // var_dump(Auth::check());
        // var_dump(Auth::viaRemember());
        // var_dump(Auth::check());
        // die();
        
        if(!Auth::check()) {
            return Response::json(json_decode("{}"));
        }
        return Response::json(Auth::user());
    });
    Route::get('session/check', function()
    {
        
        var_dump(Auth::viaRemember());
        var_dump(Auth::check());
        var_dump(Auth::viaRemember());
        var_dump(Auth::check());
        die();
    });

    Route::post('users/login', 'UsersController@login');
    Route::get('users/logout', 'UsersController@logout');
    Route::post('users/logout', 'UsersController@logout');
    Route::post('users/register', 'UsersController@register');
    Route::resource('users', 'UsersController');
    Route::resource('users.projects', 'UsersProjectsController');
    Route::resource('users.projects.posts', 'UsersProjectsPostsController');

    // Me
    Route::group(array('prefix' => 'me', 'before' => 'auth'), function() {
        Route::get('user', function() {
            $user = Auth::user();
            return Response::json($user);
        });

        Route::resource('notifications', 'MeNotificationsController');

        Route::resource('projects', 'MeProjectsController');
        Route::resource('projects.screenshots', 'MeProjectsScreenshotsController');
        Route::resource('projects.directories', 'MeProjectsWatchesController');
        Route::resource('projects.watches', 'MeProjectsWatchesController');
        Route::resource('projects.progress', 'MeProjectsProgressController');

        
        // Make sure that the resource is after the group
        // otherwise it captures the route
        Route::group(array('prefix' => 'following'), function() {
            Route::resource('posts', 'MeFollowingPostsController');
            Route::get('leaderboard', 'LeaderboardController@leaderboard');
            Route::get('online', 'OnlineController@online');
        });
        Route::resource('following', 'MeFollowingController');

        Route::resource('posts', 'MePostsController');
        Route::resource('posts.likes', 'PostsLikesController');
        Route::resource('posts.comments', 'PostsCommentsController');
    });

    // Post
    Route::resource('posts', 'PostsController');
    Route::resource('posts.likes', 'PostsLikesController');
    Route::resource('posts.comments', 'PostsCommentsController');
});

// });
