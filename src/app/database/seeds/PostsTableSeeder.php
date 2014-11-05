<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Carbon\Carbon;

class PostsTableSeeder extends Seeder {

  public function run()
  {
        DB::table('posts')->truncate();
        DB::table('comments')->truncate();
        DB::table('likes')->truncate();
        DB::table('media')->truncate();

        include 'progress-db.php';

        foreach($post as $existingPost) {
            if($existingPost['type'] == 'STARTED_WORKING') {
                continue;
            }
            $newPost = new Post;
            $newPost->id = $existingPost['id'];
            if(!$existingPost['user_id']) {
                continue;
            }
            $newPost->user_id = $existingPost['user_id'];
            if(!$existingPost['project_id']) {
                continue;
            }
            $newPost->project_id = $existingPost['project_id'];
            $newPost->type = $existingPost['type'];
            if(!$existingPost['text']) {
                $existingPost['text'] = '';
            }
            $newPost->text = $existingPost['text'];
            $newPost->type = $existingPost['type'];
            $newPost->created_at = Carbon::createFromTimeStamp($existingPost['created']);
            $newPost->updated_at = Carbon::createFromTimeStamp($existingPost['modified']);
            $newPost->save();
        }

        // Do the comments
        foreach($comment as $existingComment) {
            $newComment = new Comment;
            $newComment->id = $existingComment['id'];
            $newComment->text = $existingComment['text'];
            $newComment->user_id = $existingComment['user_id'];
            if(!$existingComment['post_id']) {
                continue;
            }
            $newComment->post_id = $existingComment['post_id'];
            $newComment->save();
        }

        foreach($like as $existingLike) {
            // array('id' => '1','user_id' => '1','post_id' => '225'),
            $newLike = new Like;
            $newLike->id = $existingLike['id'];
            $newLike->user_id = $existingLike['user_id'];
            if(!$existingLike['post_id']) {
                continue;
            }
            $newLike->post_id = $existingLike['post_id'];
            $newLike->save();
        }

        //  Do the files....eep
        foreach($file as $existingFile) {
          
            // array('id' => '1',
            // 'name' => 'Screen Shot 2014-07-09 at 9.55.49 pm.jpg',
            // 'user_id' => '1',
            // 'project_id' => '1',
            // 'collection_id' => '1'),
            $newMedia = new Media;
            $newMedia->filename = $existingFile['name'];
            $newMedia->path = '';
            $newMedia->type = 'SCREENSHOT';
            $newMedia->url = 'http://ec2-54-206-66-123.ap-southeast-2.compute.amazonaws.com/progress/uploads/' . $newMedia->filename;
            foreach($post as $existingPost) {
                if($existingPost['collection_id'] === $existingFile['collection_id']) {
                    $newMedia->post_id = $existingPost['id'];
                }
            }
            if(!$newMedia->post_id) {
                continue;
            }
            $newMedia->save();
        }
    // $faker = Faker::create();

  //       $firstUser = User::all()->first();
  //       $userCount = User::all()->count();

  //       $firstProject = Project::all()->first();
  //       $projectCount = Project::all()->count();


    // foreach(range(1, 10) as $index)
    // {
    //  $post = Post::create([
  //               'text' => $faker->sentence,
  //               'user_id' => $firstUser->id + rand(0, $userCount),
  //               'project_id' => $firstProject->id + rand(0, $projectCount),
  //               'type' => 'TEXT'
    //  ]);
  //           $post->save();

  //           foreach(range(1, rand(1, 5)) as $index2)
  //           {
  //               $comment = Comment::create([
  //                   'text' => $faker->sentence,
  //                   'post_id' => $post->id,
  //                   'user_id' => $firstUser->id + rand(0, $userCount)
  //               ]);
  //               $comment->save();
  //           }
  //           foreach(range(0, rand(0, 1)) as $index3) {
  //               $like = Like::create([
  //                   'post_id' => $post->id,
  //                   'user_id' => $firstUser->id + rand(0, $userCount)
  //               ]);
  //           }
    // }
  }
}
