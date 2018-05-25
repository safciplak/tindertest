<?php

namespace App\Http\Controllers;

use App\InstagramPhotos;
use App\Jobs\LikeUser;
use App\Jobs\SaveUserInformation;
use App\Jobs\UnlikeUser;
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
