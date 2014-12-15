<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	protected $fillable = ['name','username','email','password','picture'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token', 'email');

	public function posts() {
		return $this->hasMany('Post');
	}
	public function projects() {
		return $this->hasMany('Project');
	}
	public function notifications() {
		return $this->hasMany('Notification');
	}
	public function follows() {
		return $this->hasMany('Follow');
	  // return $this->belongsToMany('User', 'users_follows', 'user_id', 'followee_id');
	}
	public function followers() {
	  return $this->belongsToMany('User', 'users_follows', 'followee_id', 'user_id');
	}

	public function getFeed() {
		return Post::whereIn('project_id', function($query) {
		  $query->select('project_id')
		        ->from('follows')
		        ->where('user_id', $this->id);
		})->with('user', 'project', 'media', 'likes', 'likes.user', 'comments', 'comments.user')->orderby('created_at', 'desc')->take(10)->get();
	}

	public function getActiveProject() {
		$progress = $this->getLastProgress();
		if(!$progress || !$progress->isRecent()) {
			return false;
		}
		return $progress->project;
	}

	public function getIsOnline() {
		$progress = $this->getLastProgress();
		if(!$progress || !$progress->isRecent()) {
			return false;
		}
		return true;
	}
	public function getLastProgress() {
        $progress = Progress::where('user_id', '=', $this->id)->orderby('created_at', 'desc')->first();
        return $progress;
    }

	public function getSecondsThisWeek() {
		$seconds = 0;
		foreach($this->projects as $project) {
			$timer = $project->getThisWeeksTimer();
			if($timer) {
				$seconds += $timer->seconds;
			}
		}
		return $seconds;
	}

	public function getIsFollowing($userId) {
		return $this->follows()->where('followee_id', '=', $userId)->count() > 0;
	}
	// public function getRememberToken()
	// {
	//     return $this->remember_token;
	// }

	// public function setRememberToken($value)
	// {
	//     $this->remember_token = $value;
	// }

	// public function getRememberTokenName()
	// {
	//     return 'remember_token';
	// }
}
