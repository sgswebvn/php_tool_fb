<?php

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = [])
    {
        extract($data);
        require __DIR__ . "/../Views/{$view}.php";
    }

    protected function redirect(string $url)
    {
        header("Location: " . $url);
        exit;
    }

    protected function dd($data)
    {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        die();
    }

    protected function url(string $path = ""): string
    {
        return "http://localhost/" . ltrim($path, "/");
    }
}
