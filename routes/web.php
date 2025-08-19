<?php

// Guest routes
$router->get('/', ['GuestController', 'home']);
$router->get('/services', ['GuestController', 'services']);
$router->get('/about', ['GuestController', 'about']);
$router->get('/register', ['AuthController', 'showRegisterForm']);
$router->post('/register', ['AuthController', 'register']);
$router->get('/login', ['AuthController', 'showLoginForm']);
$router->post('/login', ['AuthController', 'login']);
$router->post('/logout', ['AuthController', 'logout']);

// User routes
$router->get('/dashboard', ['UserDashboardController', 'index']);
$router->get('/settings', ['UserDashboardController', 'settings']);
$router->get('/transactions', ['UserDashboardController', 'transactions']);

// Facebook routes
$router->get('/facebook/connect', ['FacebookController', 'redirectToFacebook']);
$router->get('/facebook/callback', ['FacebookController', 'handleCallback']);

// Webhook route
$router->post('/webhook', ['WebhookController', 'handle']);

// Admin routes (cần middleware auth và admin check)
$router->get('/admin/stats', ['AdminController', 'stats']);
$router->get('/admin/users', ['AdminController', 'users']);
$router->get('/admin/plans', ['AdminController', 'plans']);
$router->post('/admin/plans/update', ['AdminController', 'updatePlan']);
$router->post('/admin/users/block/{id}', ['AdminController', 'blockUser']);
