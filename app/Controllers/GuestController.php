<?php

namespace App\Controllers;

use App\Core\Controller;

class GuestController extends Controller
{
    public function home()
    {
        $this->view('guest/home', ['title' => 'Trang chủ']);
    }

    public function services()
    {
        $this->view('guest/services', ['title' => 'Dịch vụ']);
    }

    public function about()
    {
        $this->view('guest/about', ['title' => 'Giới thiệu']);
    }
}
