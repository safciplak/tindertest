<?php

namespace App\Jobs;

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

                dump($item);


                $user = User::create([
                    'user_id' => $userId,
                    'bio' => $bio,
                    'name' => $name,
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
