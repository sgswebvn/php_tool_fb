<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Models\UserSocialAccount;
use App\Models\Page;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AuditLog;

class FacebookController extends Controller
{
    private function curlRequest($url, $method = 'GET', $params = [], $accessToken = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($accessToken) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        }

        if (!empty($params) && $method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("cURL Error: " . $error);
            return false;
        }
        return json_decode($response, true);
    }

    public function redirectToFacebook()
    {
        $state = bin2hex(random_bytes(16)); // Tạo state ngẫu nhiên
        Session::set('fb_state', $state);   // Lưu state vào session
        $params = [
            'client_id' => env('FB_APP_ID'),
            'redirect_uri' => $this->url('/facebook/callback'),
            'scope' => 'email,pages_show_list,pages_manage_posts,pages_messaging,pages_read_engagement,read_insights',
            'response_type' => 'code',
            'state' => $state
        ];
        $loginUrl = 'https://www.facebook.com/v23.0/dialog/oauth?' . http_build_query($params);
        Response::redirect($loginUrl);
    }

    public function handleCallback()
    {
        error_log("Callback received: " . print_r($_GET, true)); // Log query string
        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        $sessionState = Session::get('fb_state');

        if (!$code || !$state || $state !== $sessionState) {
            error_log("Invalid code or state mismatch: code=$code, state=$state, sessionState=$sessionState");
            Response::json(['error' => 'Invalid authentication request'], 400);
            return;
        }

        // Lấy access token
        $tokenUrl = 'https://graph.facebook.com/v23.0/oauth/access_token';
        $tokenParams = [
            'client_id' => env('FB_APP_ID'),
            'client_secret' => env('FB_APP_SECRET'),
            'redirect_uri' => $this->url('/facebook/callback'),
            'code' => $code
        ];
        $tokenResponse = $this->curlRequest($tokenUrl, 'GET', $tokenParams);
        error_log("Token response: " . print_r($tokenResponse, true));

        if (!$tokenResponse || isset($tokenResponse['error'])) {
            error_log("Token error: " . ($tokenResponse['error']['message'] ?? 'No token received'));
            Response::json(['error' => 'Failed to get access token'], 400);
            return;
        }

        $accessToken = $tokenResponse['access_token'];
        $userId = Session::get('user_id');

        // Lấy thông tin user
        $meResponse = $this->curlRequest("https://graph.facebook.com/v23.0/me", 'GET', [], $accessToken);
        if (!$meResponse || isset($meResponse['error'])) {
            error_log("Me error: " . ($meResponse['error']['message'] ?? 'No user data'));
            Response::json(['error' => 'Failed to get user info'], 400);
            return;
        }
        $fbUserId = $meResponse['id'];

        UserSocialAccount::create([
            'user_id' => $userId,
            'provider' => 'facebook',
            'fb_user_id' => $fbUserId,
            'access_token' => $accessToken,
            'token_expires' => date('Y-m-d H:i:s', time() + $tokenResponse['expires_in'])
        ]);

        // Lấy danh sách fanpage
        $pagesResponse = $this->curlRequest("https://graph.facebook.com/v23.0/me/accounts", 'GET', [], $accessToken);
        if (!$pagesResponse || isset($pagesResponse['error'])) {
            error_log("Pages error: " . ($pagesResponse['error']['message'] ?? 'No pages data'));
            Response::json(['error' => 'Failed to get pages'], 400);
            return;
        }
        $pages = $pagesResponse['data'] ?? [];
        foreach ($pages as $pageData) {
            $pageId = Page::create([
                'user_id' => $userId,
                'fb_page_id' => $pageData['id'],
                'name' => $pageData['name'],
                'category' => $pageData['category'] ?? '',
                'access_token' => $pageData['access_token']
            ]);

            // Lấy posts và comments
            $postsResponse = $this->curlRequest("https://graph.facebook.com/v23.0/{$pageData['id']}/posts", 'GET', [], $pageData['access_token']);
            $posts = $postsResponse['data'] ?? [];
            foreach ($posts as $postData) {
                $postId = Post::create([
                    'page_id' => $pageId,
                    'fb_post_id' => $postData['id'],
                    'message' => $postData['message'] ?? '',
                    'created_time' => date('Y-m-d H:i:s', strtotime($postData['created_time'])),
                    'status' => 'published'
                ]);
                $commentsResponse = $this->curlRequest("https://graph.facebook.com/v23.0/{$postData['id']}/comments", 'GET', [], $pageData['access_token']);
                $comments = $commentsResponse['data'] ?? [];
                foreach ($comments as $commentData) {
                    Comment::create([
                        'post_id' => $postId,
                        'fb_comment_id' => $commentData['id'],
                        'parent_comment_id' => $commentData['parent'] ? $commentData['parent']['id'] : null,
                        'from_id' => $commentData['from']['id'],
                        'from_name' => $commentData['from']['name'] ?? '',
                        'message' => $commentData['message'] ?? '',
                        'created_time' => date('Y-m-d H:i:s', strtotime($commentData['created_time']))
                    ]);
                }
            }

            // Lấy conversations và messages
            $convResponse = $this->curlRequest("https://graph.facebook.com/v23.0/{$pageData['id']}/conversations", 'GET', [], $pageData['access_token']);
            $conversations = $convResponse['data'] ?? [];
            foreach ($conversations as $convData) {
                $convId = Conversation::create([
                    'page_id' => $pageId,
                    'fb_conversation_id' => $convData['id'],
                    'customer_psid' => $convData['participants']['data'][0]['id'],
                    'last_message_time' => date('Y-m-d H:i:s', strtotime($convData['updated_time']))
                ]);
                $msgResponse = $this->curlRequest("https://graph.facebook.com/v23.0/{$convData['id']}/messages", 'GET', [], $pageData['access_token']);
                $messages = $msgResponse['data'] ?? [];
                foreach ($messages as $msgData) {
                    Message::create([
                        'conversation_id' => $convId,
                        'fb_message_id' => $msgData['id'],
                        'sender_id' => $msgData['from']['id'],
                        'recipient_id' => $msgData['to']['data'][0]['id'],
                        'message' => $msgData['message'] ?? '',
                        'created_time' => date('Y-m-d H:i:s', strtotime($msgData['created_time'])),
                        'direction' => $msgData['from']['id'] == $pageData['id'] ? 'out' : 'in'
                    ]);
                }
            }
        }

        AuditLog::log(['action' => 'fb_connect_success', 'user_id' => $userId]);
        Response::redirect('/dashboard');
    }
}
