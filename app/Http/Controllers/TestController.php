<?php

namespace App\Http\Controllers;

use App\InstagramPhotos;
use App\Jobs\SaveUserInformation;
use App\School;
use App\User;
use App\UserImages;
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
}
