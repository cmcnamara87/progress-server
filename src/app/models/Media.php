<?php

class Media extends \Eloquent {
	protected $fillable = [];

    protected $table = 'media';

    public function post() {
        return $this->belongsTo('Post');
    }
}