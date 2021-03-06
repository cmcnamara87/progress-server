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

        // Add progress
        $project = Project::find($projectId);
        $project->addProgress($userId);

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

        // Add progress
        $project = Project::find($projectId);
        $project->addProgress($userId);

        $img = Image::make($inputFile);
        $img->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save();

        
        if (App::environment('production')) {
            $uploadDir = '/home/cmcnamara87/www/getprogress.com/uploads';
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
        $newComment = new Comment;
        $newComment->post_id = $this->id;
        $newComment->user_id = $userId;
        $newComment->text = $text;
        $this->comments()->save($newComment);

        // Get some eager lazy loading for the emails
        $newComment->load('user');
        $this->load('user');

        // Not commenting on your own post
        if($userId != $this->user_id) {
            // Notify the post author
            $notification = new Notification;
            $notification->text = $newComment->user->name . ' commented on your post.<br/>"' . $text . '"';
            $notification->isread = 0;
            $notification->user_id = $this->user->id;
            $notification->post_id = $this->id;
            $notification->save();

            $post = $this;
            $data = array(
                'post' => $this,
                'comment' => $newComment
            );
            Mail::queue('emails.notifications.comment', $data, function($message) use ($post, $newComment) {
                $message->to($post->user->email, $post->user->name)
                      ->subject($newComment->user->name . ' commented on your post');
            });
        }
        
        // Notify the other commenters (except the post author, and the commenter)
        $userIds = array();

        foreach($this->comments as $existingComment) {
            if($existingComment->user_id == $userId || $existingComment->user_id == $this->user_id) {
                continue;
            }
            $userIds[] = $existingComment->user->id;
            $userIds = array_unique($userIds);
        }

        foreach($userIds as $userId) {
            // Let all the other comments know
            $notifyUser = User::find($userId);

            $notification = new Notification;
            $notification->text = $newComment->user->name . ' commented on ' . $this->user->name . '\'s post.<br/>"' . $text . '"';
            $notification->isread = 0;
            $notification->user_id = $notifyUser->id;
            $notification->post_id = $this->id;
            $notification->save();

            $post = $this;
            $data = array(
                'post' => $this,
                'comment' => $newComment
            );
            Mail::queue('emails.notifications.othercomment', $data, function($message) use ($post, $newComment, $notifyUser) {
                $message->to($notifyUser->user->email, $notifyUser->user->name)
                      ->subject($newComment->user->name . ' commented on ' . $post->user->name . 's post');
            });
        }

        return $newComment;
    }
    public function addLike($userId) {
        // Ignore duplicate likes
        $like = $this->likes()->where('user_id', '=', $userId)->first();
        if($like) {
            return $like;
        }

        // Make a new like
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

        $post = $this;
        $data = array(
            'post' => $this,
            'like' => $like
        );
        Mail::queue('emails.notifications.like', $data, function($message) use ($post, $like) {
            $message->to($post->user->email, $post->user->name)
                  ->subject($like->user->name . ' liked your post');
        });

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