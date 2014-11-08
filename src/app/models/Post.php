<?php

class Post extends \Eloquent {
	protected $fillable = ['text', 'user_id', 'project_id'];

    public static $rules = [
        // 'title' => 'required'
    ];
    
    public static function createTextPost($text, $userId, $projectId) {
        $post = new Post;
        $post->user_id = $userId;
        $post->project_id = $projectId;
        $post->text = $text;
        $post->type = 'TEXT';
        $post->save();
        return $post;
    }
    /**
     * 
     * @param  [type] $text      [description]
     * @param  [type] $userId    [description]
     * @param  [type] $projectId [description]
     * @param  [type] $inputFile Input from post request, e.g. Input::file('picture')
     * @return [type]            [description]
     */
    public static function createPicturePost($text, $userId, $projectId, $inputFile) {

        $post = new Post;
        $post->user_id = $userId;
        $post->project_id = $projectId;
        $post->text = $text;
        $post->type = 'SCREENSHOT_COLLECTION';
        $post->save();

        $img = Image::make($inputFile);
        $img->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save();

        
        if (App::environment('production')) {
            $uploadDir = '/var/www/html/progress/uploads/';
        }
        if (App::environment('local')) {
            $uploadDir = '/Users/cmcnamara87/Sites/progress/progress-server-v3/files';
        }
        
        $fileName = 'user_' . $userId . '_post_' . $post->id . '_' . $inputFile->getClientOriginalName();
        $inputFile->move($uploadDir, $fileName);

        $media = new Media;
        $media->path = $uploadDir . '/' . $fileName;
        $media->filename = $fileName;

        if (App::environment('production')) {
            $media->url = 'http://getprogress.com/uploads/' . $fileName;
        }
        if (App::environment('local')) {
            $media->url = 'http://localhost:8888/files/' . $fileName;
        }
        $media->type = 'SCREENSHOT';

        $post->media()->save($media);

        return $post;
    }

    public function addComment($text, $userId) {
        $comment = new Comment;
        $comment->post_id = $this->id;
        $comment->user_id = $userId;
        $comment->text = $text;
        $this->comments()->save($comment);


        // Notify the post author
        $notification = new Notification;
        $notification->text = $comment->user->name . ' commented on your post.<br/>"' . $text . '"';
        $notification->isread = 0;
        $notification->user_id = $this->user->id;
        $notification->post_id = $this->id;
        $notification->save();

        // Notify the other commenters
        foreach($this->comments as $existingComment) {
            if($existingComment->user->id == $comment->user->id || $existingComment->user->id == $this->user->id) {
                continue;
            }
            // Let all the other comments know
            $notification = new Notification;
            $notification->text = $comment->user->name . ' commented on ' . $this->user->name . '\'s post.<br/>"' . $text . '"';
            $notification->isread = 0;
            $notification->user_id = $existingComment->user->id;
            $notification->post_id = $this->id;
            $notification->save();
        }

        return $comment;
    }
    public function addLike($userId) {
        $like = new Like;
        $like->post_id = $this->id;
        $like->user_id = $userId;
        $this->likes()->save($like);

        // Make the notification
        $notification = new Notification;
        $notification->text = $like->user->name . ' liked your post.';
        $notification->isread = 0;
        $notification->user_id = $this->user->id;
        $notification->post_id = $this->id;
        $notification->save();

        return $like;
    }

    public function comments() {
        return $this->hasMany('Comment');
    }
    public function user() {
        return $this->belongsTo('User');
    }
    public function project() {
        return $this->belongsTo('Project');
    }
    public function likes() {
        return $this->hasMany('Like');
    }
    public function media() {
        return $this->hasMany('Media');
    }
}