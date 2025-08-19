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
        // Kiểm tra CSRF token
        if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
            Response::json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        // Validation
        $name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
        $email = trim(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
        $password = $_POST['password'];
        if (!$name || !$email || strlen($password) < 8) {
            Response::json(['error' => 'Invalid input'], 400);
            return;
        }
        $existing = User::firstWhere('email', $email);
        if ($existing) {
            Response::json(['error' => 'Email exists'], 400);
            return;
        }

        // Tạo user
        $id = User::create(['name' => $name, 'email' => $email, 'password' => $password]);
        if ($id) {
            Subscription::create(['user_id' => $id, 'plan_id' => 1, 'status' => 'active', 'expires_at' => null]); // Free plan
            AuditLog::log(['action' => 'user_register', 'user_id' => $id]);
            Response::redirect('/login');
        } else {
            Response::json(['error' => 'Registration failed'], 500);
        }
    }

    public function showLoginForm()
    {
        $csrf = generate_csrf_token();
        $this->view('guest/login', ['title' => 'Đăng nhập', 'csrf' => $csrf]);
    }

    public function login()
    {
        // Kiểm tra CSRF token
        if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
            Response::json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $email = trim(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
        $password = $_POST['password'];
        if (!$email || !$password) {
            Response::json(['error' => 'Invalid input'], 400);
            return;
        }
        $user = User::firstWhere('email', $email);
        if ($user && password_verify($password, $user['password'])) {
            Session::start();
            Session::set('user_id', $user['id']);
            AuditLog::log(['action' => 'user_login', 'user_id' => $user['id']]);
            Response::redirect('/dashboard');
        } else {
            Response::json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function logout()
    {
        $userId = Session::get('user_id');
        AuditLog::log(['action' => 'user_logout', 'user_id' => $userId]);
        Session::destroy();
        Response::redirect('/login');
    }
}
