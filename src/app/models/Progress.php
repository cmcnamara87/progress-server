<?php

use Carbon\Carbon;

class Progress extends \Eloquent {
	protected $fillable = [];

    protected $table = 'progress';

    public function project() {
        return $this->belongsTo('Project');
    }
    public function isRecent() {
        return $this->created_at->gte(Carbon::now()->subHours(1));
    }
}