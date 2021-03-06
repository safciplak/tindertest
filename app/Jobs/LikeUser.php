<?php

namespace App\Jobs;

use App\Match;
use App\Member;
use App\Setting;
use App\User;
use App\UserLikes;
use App\UserUnLikes;
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

        $users = User::with('userImages')
            ->where('id', '>', Setting::where('key', 'mustafa_last_user_id')->value('value'))
            ->limit(30)->get();


        foreach ($users as $user) {
            $userId = $user->user_id;
            if(isset($user->lat) && isset($user->lng)){
                $tinder->updateLocation($user->lat, $user->lng);
            }
//            $existUserLike = User::where('user_id', $userId)->first();
//            if (!empty($existUserLike)) {
//                $user = User::with('userImages')->whereNotNull('instagram')->inRandomOrder()->first();
//                $userId = $user->user_id;
//            }

//            $userId = '5aec30598212bfdb6c2da6dc'; // ahmet
//            $userId = '56c184752311a9ee6e827894'; // safak

//            $likeUnlikeArray = [false, true];
//            $select = rand(0, 1);
//
//            if ($likeUnlikeArray[$select]) {
                $likeResult = $tinder->like($userId);

                if (isset($likeResult->status)) {
                    if ($likeResult->status != 200) {
                        dd($likeResult->status);
                    }
                }

                if (isset($likeResult->match)) {
                    if ($likeResult->match != false) {
                        Match::updateOrCreate([
                            'user_id' => $userId
                        ],[]);
                    }
                }

                UserLikes::updateOrCreate([
                    'user_id' => $userId
                ], []);
//            } else {
//                $tinder->pass($userId);
//                UserUnLikes::updateOrCreate([
//                    'user_id' => $userId
//                ], []);
//            }


            Setting::where('key', 'mustafa_last_user_id')
                ->update([
                    'value' => $user->id
                ]);
        }

    }
}
