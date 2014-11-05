<?php

class Comment extends \Eloquent {
	protected $fillable = ['text', 'user_id', 'post_id'];

    public static $rules = [
        // 'title' => 'required'
    ];

    public function post()
    {
        return $this->belongsTo('Post');
    }
    public function user()
    {
        return $this->belongsTo('User');
    }
}