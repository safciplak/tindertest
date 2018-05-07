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

        $client = new \GuzzleHttp\Client();

        $res = $client->request('GET', 'https://www.facebook.com/v2.6/dialog/oauth?redirect_uri=fb464891386855067%3A%2F%2Fauthorize%2F&state=%7B%22challenge%22%3A%22q1WMwhvSfbWHvd8xz5PT6lk6eoA%253D%22%2C%220_auth_logger_id%22%3A%2254783C22-558A-4E54-A1EE-BB9E357CC11F%22%2C%22com.facebook.sdk_client_state%22%3Atrue%2C%223_method%22%3A%22sfvc_auth%22%7D&scope=user_birthday%2Cuser_photos%2Cuser_education_history%2Cemail%2Cuser_relationship_details%2Cuser_friends%2Cuser_work_history%2Cuser_likes&response_type=token%2Csigned_request&default_audience=friends&return_scopes=true&auth_type=rerequest&client_id=464891386855067&ret=login&sdk=ios&logger_id=54783C22-558A-4E54-A1EE-BB9E357CC11F#_=', [
            'email' => 'safak.ciplak',
            'password' => 'Endi1453*'
        ]);

//        $res = $client->request('POST', 'https://www.facebook.com/v2.6/dialog/oauth/confirm?dpr=2');
        echo $res->getBody()->getContents();

//        LikeUser::dispatch();
    }

    public function unlike()
    {
        UnLikeUser::dispatch();
    }
}
