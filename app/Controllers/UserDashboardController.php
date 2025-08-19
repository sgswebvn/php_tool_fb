<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Page;
use App\Models\Post;
use App\Models\Conversation;
use App\Models\Comment;
use App\Models\Subscription;
use App\Models\Payment;

class UserDashboardController extends Controller
{
    public function index()
    {
        $userId = Session::get('user_id');
        $pages = Page::getByUserId($userId);
        $selectedPageId = $_GET['page_id'] ?? ($pages[0]['id'] ?? null);
        $posts = $selectedPageId ? Post::getByPageId($selectedPageId) : [];
        $conversations = $selectedPageId ? Conversation::getByPageId($selectedPageId) : [];
        $subscription = Subscription::getActiveByUserId($userId);
        $plan = $subscription ? \App\Models\Plan::find($subscription['plan_id']) : null;
        $payments = Payment::getByUserId($userId);

        $this->view('user/dashboard', [
            'pages' => $pages,
            'posts' => $posts,
            'conversations' => $conversations,
            'selectedPageId' => $selectedPageId,
            'subscription' => $subscription,
            'plan' => $plan,
            'payments' => $payments,
            'title' => 'Dashboard Người dùng'
        ]);
    }

    public function settings()
    {
        $userId = Session::get('user_id');
        $user = \App\Models\User::find($userId);
        $this->view('user/settings', ['user' => $user, 'title' => 'Cài đặt']);
    }

    public function transactions()
    {
        $userId = Session::get('user_id');
        $payments = \App\Models\Payment::getByUserId($userId);
        $this->view('user/transactions', ['payments' => $payments, 'title' => 'Lịch sử Giao dịch']);
    }
}
