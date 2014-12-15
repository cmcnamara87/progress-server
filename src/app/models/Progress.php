<?php

use Carbon\Carbon;

class Progress extends \Eloquent {
	protected $fillable = [];

    protected $table = 'progress';

    private function getRecentCutoffTime() {
        return Carbon::now()->subHours(1);
    }
    public function scopeIsRecent($query) {
        return $query->where('created_at', '>', $this->getRecentCutoffTime()->toDateTimeString());
    }
    public function project() {
        return $this->belongsTo('Project');
    }
    public function user() {
        return $this->belongsTo('User');
    }
    public function isRecent() {
        return $this->created_at->gte($this->getRecentCutoffTime());
    }
}