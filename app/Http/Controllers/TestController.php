<?php

namespace App\Http\Controllers;

use App\Jobs\SaveUserInformation;
use App\User;
use App\UserImages;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $fbUserId = 10152410516074925;
        $fbToken = 'EAAGm0PX4ZCpsBABCpU2GHrAQpZBwBRrWGfl1aB7nMjdZCcbqWlYTuUNmLDfYLgc09PI3RxZAPjkKhfzSdUYWd9rcnAkGL3k47ZCZBUKrI3rpzNpbWbTJP6IG22KWUZAt4d8hNJtEZAZAzM8C7D71YyXMnbQiZBQ8Fa4SylbAZAoM0Vq4AZBw5TkbSCzrXjbTZB5MsbqmgzzPa48EWZChDRhLGydEdEohMDlfZCYZAb9Iyve0zvsslGeIHla6V0ivxBaMCEQt61gZD';
        $tinder = new \Pecee\Http\Service\Tinder($fbUserId, $fbToken);
        $tinder->updateLocation(41.060084,28.9793561);

        $x = $tinder->recommendations();

        if ($x->status != 200){
            dd('accessToken');
        }

        if ($x->status == 200)
            foreach ($x->results as $item) {
                $userId = $item->_id;
                $bio = $item->bio;
                $name = $item->name;
                $birthDate = $item->birth_date;
                $distanceMile = $item->distance_mi;

                $birthDate = explode("T",$birthDate);
                $birthDate = explode("-", reset($birthDate));
                $age = date('Y') - reset($birthDate);

                $instagram = null;
                if(isset($item->instagram)){
                    $instagram = $item->instagram->username;
                    $instagramImageCount = $item->instagram->media_count;
                }

                dump($item);


                $user = User::create([
                    'user_id' => $userId,
                    'bio' => $bio,
                    'name' => $name,
//                    'instagram' => $instagram,
//                    'age' => $age,
//                    'distance_mile' => $distanceMile,
                ]);

                foreach ($item->photos as $photo) {
                    $photoUrl = $photo->url;
                    UserImages::create([
                        'user_id' => $user->id,
                        'photo' => $photoUrl
                    ]);
                }
            }
    }

    public function show()
    {
        $images = UserImages::all();

        return view('welcome', compact('images'));
    }
}
