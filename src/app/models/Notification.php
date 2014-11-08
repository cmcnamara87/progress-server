<?php

class Notification extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

    // protected $table = 'notifications';

	// Don't forget to fill this array
	protected $fillable = [];

    public function user() {
        return $this->belongsTo('User');
    }
    public function post() {
        return $this->belongsTo('Post');
    }

    public function scopeUnread($query)
    {
        return $query->where('isread', '=', false);
    }
    public function scopeRead($query)
    {
        return $query->where('isread', '=', true);
    }

    public static function findUnreadForUser($user) {
        // $user = User::findOrFail($userId);
        return $user->notifications()->unread()->orderBy('created_at', 'desc')->get();
    }
}
