<?php

namespace App\Jobs;

use App\Match;
use App\Setting;
use App\User;
use App\UserLikes;
use App\UserUnLikes;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RemoveUnlikeUsers implements ShouldQueue
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




        $users = UserUnLikes::with('user')
            ->where('id', '>', Setting::where('key', 'remove_user_id')->value('value'))
            ->limit(30)->get();

        foreach ($users as $user) {
            $userId = $user->user_id;
            if(isset($user->user->lat) && isset($user->user->lng)){
                $tinder->updateLocation($user->user->lat, $user->user->lng);
            }

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


            Setting::where('key', 'remove_user_id')
                ->update([
                    'value' => $user->id
                ]);

            UserUnLikes::destroy(Setting::where('key', 'remove_user_id')->value('value'));
        }

    }
}
