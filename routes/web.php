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
$router->get('/dashboard', ['UserController', 'dashboard']);
$router->get('/fanpages', ['UserController', 'fanpages']);
$router->get('/posts', ['UserController', 'posts']);
$router->post('/posts/create', ['UserController', 'createPost']);
$router->post('/posts/delete/{id}', ['UserController', 'deletePost']);
$router->get('/messages', ['UserController', 'messages']);
$router->post('/messages/send', ['UserController', 'sendMessage']);
$router->post('/conversations/delete/{id}', ['UserController', 'deleteConversation']);
$router->get('/settings', ['UserController', 'settings']);
$router->get('/transactions', ['UserController', 'transactions']);
$router->get('/subscription', ['UserController', 'subscription']);

// Facebook routes
$router->get('/facebook/connect', ['FacebookController', 'redirectToFacebook']);
$router->get('/facebook/callback', ['FacebookController', 'handleCallback']);

// Webhook route
$router->post('/webhook', ['WebhookController', 'handle']);

// Admin routes
$router->get('/admin/stats', ['AdminController', 'stats']);
$router->get('/admin/users', ['AdminController', 'users']);
$router->post('/admin/users/block/{id}', ['AdminController', 'blockUser']);
$router->get('/admin/plans', ['AdminController', 'plans']);
$router->post('/admin/plans/update', ['AdminController', 'updatePlan']);

$router->get('/home/test', ['Test', 'index']);
