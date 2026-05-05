<?php

namespace App\Controllers;

use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\View\View;

class HomeController
{
    public function index(Request $request)
    {
        return View::make('home');
    }
}
