<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    public function get($uri, $cb)
    {
        $this->routes['GET'][$uri] = $cb;
    }
    public function post($uri, $cb)
    {
        $this->routes['POST'][$uri] = $cb;
    }
    public function put($uri, $cb)
    {
        $this->routes['PUT'][$uri] = $cb;
    }
    public function delete($uri, $cb)
    {
        $this->routes['DELETE'][$uri] = $cb;
    }

    public function dispatch($method, $uri)
    {
        $path = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');
        $cb = $this->routes[$method][$path] ?? null;
        if (!$cb) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }
        // Middleware Auth cho các route protected
        if (!in_array($path, ['/', '/register', '/login', '/facebook/connect', '/facebook/callback'])) {
            Session::start();
            if (strpos($path, '/admin') === 0) {
                if (!\App\Models\User::isAdmin(Session::get('user_id'))) {
                    Response::redirect('/dashboard');  // Hoặc 403
                }
            }
        }
        if (is_array($cb)) {
            $ctrl = "App\\Controllers\\" . $cb[0];
            $m = $cb[1];
            return (new $ctrl)->$m();
        }
        return call_user_func($cb);
    }
}
