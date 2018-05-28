<?php

namespace App\Http\Controllers;

use App\InstagramPhotos;
use App\Jobs\LikeUser;
use App\Jobs\SaveUserInformation;
use App\Jobs\UnlikeUser;
use App\Match;
use App\Member;
use App\Setting;
use App\User;
use App\UserImages;
use App\UserLikes;
use App\UserUnLikes;

class TestController extends Controller
{
    public function index()
    {
        SaveUserInformation::dispatch();
    }

    public function show()
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



        $images = UserImages::all();

        return view('welcome', compact('images'));
    }

    public function like()
    {
//        $member = Member::find(1);
//        $fbUserId = env('FB_USERID');
////        $fbUserId = $member->provider_id;
//        $fbToken = env('FB_ACCESS_TOKEN');
////        $fbToken = $member->token;
//
//        $tinder = new \Pecee\Http\Service\Tinder($fbUserId, $fbToken);
//
//        $x = $tinder->recommendations();
//
//
//        dd($x);

        

        LikeUser::dispatch();
    }

    public function unlike()
    {
        UnLikeUser::dispatch();
    }
}
