<?php

namespace App\Controllers;

use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\View\View;

class HomeController
{
    public function index(Request $request)
    {
        return View::make('basic.home', [
            'title' => 'Welcome to LunoxHoshizaki Lite',
            'message' => 'A beautifully modern, lightweight PHP MVC Framework.'
        ]);
    }

    public function about(Request $request)
    {
        return View::make('basic.about', [
            'title' => 'About Us'
        ]);
    }

    public function formSubmit(Request $request)
    {
        $name = $request->input('name', 'Guest');
        return View::make('basic.home', [
            'title' => 'Form Submitted',
            'message' => "Hello, {$name}! Your form was successfully submitted. CSRF validation passed."
        ]);
    }

    public function errorSide(Request $request)
    {
        return View::make('basic.error-side', [
            'title' => 'Error Side'
        ]);
    }
}
