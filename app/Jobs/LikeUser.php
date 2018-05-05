<?php

namespace App\Jobs;

use App\User;
use App\UserLikes;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LikeUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fbUserId = env('FB_USERID');
        $fbToken = env('FB_ACCESS_TOKEN');

        $tinder = new \Pecee\Http\Service\Tinder($fbUserId, $fbToken);

        $users = User::skip(UserLikes::count())->limit(10)->get();
        foreach ($users as $user) {
            $userId = $user->user_id;
            $existUserLike = User::where('user_id', $userId)->first();
            if(!empty($existUserLike)){
                $user = User::inRandomOrder()->first();
                $userId = $user->user_id;
            }
            $x = $tinder->like($userId);
            sleep(4);


            UserLikes::updateOrCreate([
                'user_id' => $userId
            ], []);
        }

    }
}
