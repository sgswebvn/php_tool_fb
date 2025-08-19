<?php

use App\Core\Session;

function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}
function json($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
function generate_csrf_token()
{
    Session::start();
    if (!Session::get('csrf_token')) {
        Session::set('csrf_token', bin2hex(random_bytes(32)));
    }
    return Session::get('csrf_token');
}

function validate_csrf_token($token)
{
    Session::start();
    return hash_equals(Session::get('csrf_token'), $token);
}
