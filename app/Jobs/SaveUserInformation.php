<?php

namespace App\Jobs;

use App\InstagramPhotos;
use App\Member;
use App\School;
use App\User;
use App\UserImages;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SaveUserInformation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $member = Member::find(1);
        $fbUserId = env('FB_USERID');
//        $fbUserId = $member->provider_id;
        $fbToken = env('FB_ACCESS_TOKEN');
//        $fbToken = $member->token;

        $tinder = new \Pecee\Http\Service\Tinder($fbUserId, $fbToken);
        $coordinates = [
//            [41.060084,28.9793561],
//            [40.9832818,29.0211453],
//            [41.0371106,28.9150514],
//            [41.0076428,29.0267559],
//            [41.0859612,29.0381294],
//            [41.0454028,28.9990235],
//            [46.460123,30.571703], // odessa
//            [47.4414941,30.6223387], // dinipro
            [47.4414941,30.6223387], //kiev
//            [38.4178607,26.9396314], //izmir istanbul
        ];

        $rand = rand(0, count($coordinates)-1);
        $coordinate = $coordinates[$rand];
        $lat = $coordinate[0];
        $lng = $coordinate[1];

        $tinder->updateLocation($lat, $lng);


        $city = app('geocoder')->reverse($lat, $lng)->get();

        $city = end($city);
        $city = end($city);
        $city = $city->getFormattedAddress();

        $x = $tinder->recommendations();

        if ($x->status != 200) {
            dd('accessToken');
        }

        if ($x->status == 200)
            foreach ($x->results as $item) {
                $userId = $item->_id;
                $bio = $item->bio;
                $name = $item->name;
                $birthDate = $item->birth_date;
                $distanceMile = $item->distance_mi;

                $birthDate = explode("T", $birthDate);
                $birthDate = explode("-", reset($birthDate));
                $age = date('Y') - reset($birthDate);

                if (!empty($item->schools)) {
                    $school = reset($item->schools);
                    $schoolId = $school->id ?? null;
                    $schoolName = $school->name;

                    School::updateOrCreate([
                        'school_id' => $schoolId,
                        'name' => $schoolName,
                    ], ['school_id' => $schoolId]);
                }

                $instagram = null;
                if (isset($item->instagram)) {
                    $instagram = $item->instagram->username;
                    $instagramImageCount = $item->instagram->media_count;

                    foreach ($item->instagram->photos as $photo) {
                        InstagramPhotos::create([
                            'image' => $photo->image,
                            'thumbnail' => $photo->thumbnail
                        ]);
                    }
                }

                $user = User::updateOrCreate([
                    'user_id' => $userId,
                    'bio' => $bio,
                    'name' => $name,
                    'instagram' => $instagram,
                    'instagram_image_count' => $instagramImageCount ?? null,
                    'age' => $age,
                    'distance_mile' => $distanceMile,
                    'school_id' => $schoolId ?? null,
                    'facebook_id' => $fbUserId ?? null,
                    'lat' => $lat,
                    'lng' => $lng,
                    'city' => $city
                ], []);

                foreach ($item->photos as $photo) {
                    $photoUrl = $photo->url;
                    UserImages::create([
                        'user_id' => $user->id,
                        'photo' => $photoUrl
                    ]);
                }
            }
    }
}
