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
        LikeUser::dispatch();
    }

    public function unlike()
    {
        UnLikeUser::dispatch();
    }
}
