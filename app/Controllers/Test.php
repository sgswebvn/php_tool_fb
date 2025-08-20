<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class Test extends Controller
{
    public function index()
    {
        $users = User::all();
        return $this->view('home', [
            'users' => $users,
        ]);
    }
}
