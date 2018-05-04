<?php

namespace App\Jobs;

use App\InstagramPhotos;
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
        $fbUserId = 100025789967080;
        $fbToken = 'EAAGm0PX4ZCpsBAL3IDmjWeN84zSjmOdSzw4Y6c1xMKbEqk67ZA8rJVs2zkbIme4O6fKIH4zMZBWe6EtF6bQCZBSxfxv24o2QMYPuKAysjVhEhKFUlwe9qUFN9cDzKJCiP5uEdivhuGKeKcC0S0AmdZCk4DT2OJCnNQn6A0WYMJwsrWPin5xC4JOzkXxEIhIr6ELDxzTMKsMcWII4fAM1P7V146Riuevf6zQe3K0wYqgDDW9U59qaWD90F0cGk1zTPrh5lGZC5JiwZDZD';

        $tinder = new \Pecee\Http\Service\Tinder($fbUserId, $fbToken);
        $lat = 41.060084;
        $lng = 28.9793561;
        $tinder->updateLocation($lat, $lng);

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

                $user = User::create([
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
}
