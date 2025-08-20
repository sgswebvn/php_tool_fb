<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Models\Page;
use App\Models\Post;
use App\Models\Conversation;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Models\Message;
use Facebook\Facebook;

class UserController extends Controller
{
    private $fb;

    public function __construct()
    {
        $this->fb = new Facebook([
            'app_id' => env('FB_APP_ID'),
            'app_secret' => env('FB_APP_SECRET'),
            'default_graph_version' => 'v23.0',
        ]);
    }

    public function dashboard()
    {
        $userId = Session::get('user_id');
        $pages = Page::getByUserId($userId);
        $selectedPageId = $_GET['page_id'] ?? ($pages[0]['id'] ?? null);
        $posts = $selectedPageId ? Post::getByPageId($selectedPageId) : [];
        $conversations = $selectedPageId ? Conversation::getByPageId($selectedPageId) : [];
        $subscription = Subscription::getActiveByUserId($userId);
        $plan = $subscription ? \App\Models\Plan::find($subscription['plan_id']) : null;

        $this->view('user/dashboard', [
            'pages' => $pages,
            'posts' => $posts,
            'conversations' => $conversations,
            'selectedPageId' => $selectedPageId,
            'subscription' => $subscription,
            'plan' => $plan,
            'title' => 'Dashboard'
        ]);
    }

    public function fanpages()
    {
        $userId = Session::get('user_id');
        $pages = Page::getByUserId($userId);
        $this->view('user/fanpages', ['pages' => $pages, 'title' => 'Quản lý Fanpage']);
    }

    public function posts()
    {
        $userId = Session::get('user_id');
        $pages = Page::getByUserId($userId);
        $selectedPageId = $_GET['page_id'] ?? ($pages[0]['id'] ?? null);
        $posts = $selectedPageId ? Post::getByPageId($selectedPageId) : [];
        $this->view('user/posts', ['pages' => $pages, 'posts' => $posts, 'selectedPageId' => $selectedPageId, 'title' => 'Quản lý Bài viết']);
    }

    public function createPost()
    {
        $userId = Session::get('user_id');
        $pageId = $_POST['page_id'];
        $message = trim($_POST['message']);
        $scheduledTime = $_POST['scheduled_time'] ?? null;

        if (!$pageId || !$message) {
            set_flash('error', 'Vui lòng nhập đầy đủ thông tin.');
            Response::redirect('/posts?page_id=' . $pageId);
            return;
        }

        $page = Page::find($pageId);
        if (!$page || $page['user_id'] != $userId) {
            set_flash('error', 'Không có quyền truy cập.');
            Response::redirect('/posts');
            return;
        }

        try {
            $response = $this->fb->post("/{$page['fb_page_id']}/feed", [
                'message' => $message,
                'scheduled_publish_time' => $scheduledTime ? strtotime($scheduledTime) : null,
                'published' => $scheduledTime ? false : true,
                'access_token' => $page['access_token']
            ]);
            $postData = $response->getGraphNode();
            $postId = Post::create([
                'page_id' => $pageId,
                'fb_post_id' => $postData['id'],
                'message' => $message,
                'status' => $scheduledTime ? 'scheduled' : 'published',
                'scheduled_time' => $scheduledTime,
                'created_time' => date('Y-m-d H:i:s')
            ]);
            AuditLog::log(['action' => 'post_create', 'page_id' => $pageId, 'user_id' => $userId]);
            set_flash('success', 'Đăng bài thành công.');
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            set_flash('error', 'Lỗi khi đăng bài: ' . $e->getMessage());
        }
        Response::redirect('/posts?page_id=' . $pageId);
    }

    public function deletePost($id)
    {
        $userId = Session::get('user_id');
        $post = Post::find($id);
        if (!$post || Page::find($post['page_id'])['user_id'] != $userId) {
            set_flash('error', 'Không có quyền xóa.');
            Response::redirect('/posts?page_id=' . $post['page_id']);
            return;
        }
        if (Post::delete($id)) {
            AuditLog::log(['action' => 'post_delete', 'post_id' => $id, 'user_id' => $userId]);
            set_flash('success', 'Xóa bài viết thành công.');
        } else {
            set_flash('error', 'Xóa bài viết thất bại.');
        }
        Response::redirect('/posts?page_id=' . $post['page_id']);
    }

    public function messages()
    {
        $userId = Session::get('user_id');
        $pages = Page::getByUserId($userId);
        $selectedPageId = $_GET['page_id'] ?? ($pages[0]['id'] ?? null);
        $conversations = $selectedPageId ? Conversation::getByPageId($selectedPageId) : [];
        $this->view('user/messages', ['pages' => $pages, 'conversations' => $conversations, 'selectedPageId' => $selectedPageId, 'title' => 'Quản lý Tin nhắn']);
    }

    public function sendMessage()
    {
        $userId = Session::get('user_id');
        $convId = $_POST['conv_id'];
        $message = trim($_POST['message']);

        if (!$convId || !$message) {
            set_flash('error', 'Vui lòng nhập tin nhắn.');
            Response::redirect('/messages?page_id=' . Conversation::find($convId)['page_id']);
            return;
        }

        $conv = Conversation::find($convId);
        if (!$conv || Page::find($conv['page_id'])['user_id'] != $userId) {
            set_flash('error', 'Không có quyền gửi tin nhắn.');
            Response::redirect('/messages');
            return;
        }

        $page = Page::find($conv['page_id']);
        try {
            $response = $this->fb->post("/{$page['fb_page_id']}/messages", [
                'recipient' => ['id' => $conv['customer_psid']],
                'message' => ['text' => $message],
                'access_token' => $page['access_token']
            ]);
            $msgData = $response->getGraphNode();
            Message::create([
                'conversation_id' => $convId,
                'fb_message_id' => $msgData['message_id'],
                'sender_id' => $page['fb_page_id'],
                'recipient_id' => $conv['customer_psid'],
                'message' => $message,
                'direction' => 'out'
            ]);
            AuditLog::log(['action' => 'send_message', 'conversation_id' => $convId, 'user_id' => $userId]);
            set_flash('success', 'Gửi tin nhắn thành công.');
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            set_flash('error', 'Lỗi khi gửi tin nhắn: ' . $e->getMessage());
        }
        Response::redirect('/messages?page_id=' . $conv['page_id']);
    }

    public function deleteConversation($id)
    {
        $userId = Session::get('user_id');
        $conv = Conversation::find($id);
        if (!$conv || Page::find($conv['page_id'])['user_id'] != $userId) {
            set_flash('error', 'Không có quyền xóa.');
            Response::redirect('/messages?page_id=' . $conv['page_id']);
            return;
        }
        if (Conversation::delete($id)) {
            AuditLog::log(['action' => 'conversation_delete', 'conversation_id' => $id, 'user_id' => $userId]);
            set_flash('success', 'Xóa cuộc hội thoại thành công.');
        } else {
            set_flash('error', 'Xóa cuộc hội thoại thất bại.');
        }
        Response::redirect('/messages?page_id=' . $conv['page_id']);
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

    public function subscription()
    {
        $userId = Session::get('user_id');
        $subscription = Subscription::getActiveByUserId($userId);
        $plan = $subscription ? \App\Models\Plan::find($subscription['plan_id']) : null;
        $this->view('user/subscription', ['subscription' => $subscription, 'plan' => $plan, 'title' => 'Gói hiện tại']);
    }
}
