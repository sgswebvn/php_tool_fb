<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\Subscription;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $csrf = generate_csrf_token();
        $this->view('guest/register', ['title' => 'Đăng ký', 'csrf' => $csrf]);
    }

    public function register()
    {
        if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
            set_flash('error', 'Mã bảo mật không hợp lệ.');
            Response::redirect('/register');
            return;
        }

        $name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
        $email = trim(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
        $password = $_POST['password'];
        if (!$name || !$email || strlen($password) < 8) {
            set_flash('error', 'Vui lòng nhập đầy đủ thông tin hợp lệ.');
            Response::redirect('/register');
            return;
        }

        $existing = User::firstWhere('email', $email);
        if ($existing) {
            set_flash('error', 'Email đã tồn tại.');
            Response::redirect('/register');
            return;
        }

        $id = User::create(['name' => $name, 'email' => $email, 'password' => $password]);
        if ($id) {
            Subscription::create(['user_id' => $id, 'plan_id' => 1, 'status' => 'active', 'expires_at' => null]);
            AuditLog::log(['action' => 'user_register', 'user_id' => $id]);
            set_flash('success', 'Đăng ký thành công. Vui lòng đăng nhập.');
            Response::redirect('/login');
        } else {
            set_flash('error', 'Đăng ký thất bại.');
            Response::redirect('/register');
        }
    }

    public function showLoginForm()
    {
        $csrf = generate_csrf_token();
        $this->view('guest/login', ['title' => 'Đăng nhập', 'csrf' => $csrf]);
    }

    public function login()
    {
        if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
            set_flash('error', 'Mã bảo mật không hợp lệ.');
            Response::redirect('/login');
            return;
        }

        $email = trim(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
        $password = $_POST['password'];
        if (!$email || !$password) {
            set_flash('error', 'Vui lòng nhập email và mật khẩu.');
            Response::redirect('/login');
            return;
        }

        $user = User::firstWhere('email', $email);
        if ($user && password_verify($password, $user['password'])) {
            Session::start();
            Session::set('user_id', $user['id']);
            AuditLog::log(['action' => 'user_login', 'user_id' => $user['id']]);
            set_flash('success', 'Đăng nhập thành công.');
            Response::redirect('/dashboard');
        } else {
            set_flash('error', 'Email hoặc mật khẩu không đúng.');
            Response::redirect('/login');
        }
    }

    public function logout()
    {
        $userId = Session::get('user_id');
        AuditLog::log(['action' => 'user_logout', 'user_id' => $userId]);
        Session::destroy();
        set_flash('success', 'Đăng xuất thành công.');
        Response::redirect('/login');
    }
}
