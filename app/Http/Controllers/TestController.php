<?php

namespace App\Http\Controllers;

use App\InstagramPhotos;
use App\Jobs\LikeUser;
use App\Jobs\SaveUserInformation;
use App\School;
use App\User;
use App\UserImages;
use App\UserLikes;
use Illuminate\Http\Request;

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


            UserLikes::updateOrCreate([
                'user_id' => $userId
            ], []);
        }

//        LikeUser::dispatch();
    }
}
