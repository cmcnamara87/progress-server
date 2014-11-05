<?php

use Carbon\Carbon;

class Timer extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	// protected $fillable = [];

    public static function updateProjectTimersFromProgress($project, $newProgress, $lastProgress) {
        $addedSeconds = 30;
        if(!$lastProgress) {
            $addedSeconds = 1;
        }
        if ($lastProgress && $lastProgress->isRecent()) {
            $diffInSeconds = $newProgress->created_at->diffInSeconds($lastProgress->created_at);
            $addedSeconds = min($diffInSeconds, 15 * 60);
        }
        $timer = $project->getThisWeeksTimer();
        if(!$timer) {
            $timer = new Timer;
            $timer->seconds = 0;
            $timer->type = 'week';
            $timer->project_id = $project->id;
            $timer->starting = Carbon::now()->startOfWeek();
            $timer->save();
        }
        $timer->seconds += $addedSeconds;
        $timer->save();
    }
}
