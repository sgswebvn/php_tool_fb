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
use Facebook\Facebook;

class FacebookController extends Controller
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

    public function redirectToFacebook()
    {
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email', 'pages_show_list', 'pages_manage_posts', 'pages_messaging', 'pages_read_engagement', 'read_insights'];
        $loginUrl = $helper->getLoginUrl($this->url('/facebook/callback'), $permissions);
        Response::redirect($loginUrl);
    }

    public function handleCallback()
    {
        $helper = $this->fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            set_flash('error', 'Lỗi xác thực Facebook: ' . $e->getMessage());
            Response::redirect('/dashboard');
            return;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            set_flash('error', 'Lỗi SDK Facebook: ' . $e->getMessage());
            Response::redirect('/dashboard');
            return;
        }

        if (!$accessToken) {
            set_flash('error', 'Không thể lấy token truy cập.');
            Response::redirect('/dashboard');
            return;
        }

        $oAuth2Client = $this->fb->getOAuth2Client();
        $longLivedToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        $userId = Session::get('user_id');
        $response = $this->fb->get('/me', $longLivedToken->getValue());
        $fbUserId = $response->getGraphUser()['id'];

        UserSocialAccount::create([
            'user_id' => $userId,
            'provider' => 'facebook',
            'fb_user_id' => $fbUserId,
            'access_token' => $longLivedToken->getValue(),
            'token_expires' => $longLivedToken->getExpiresAt()
        ]);

        $pageResponse = $this->fb->get('/me/accounts', $longLivedToken->getValue());
        $pages = $pageResponse->getGraphEdge()->asArray();
        foreach ($pages as $pageData) {
            $pageId = Page::create([
                'user_id' => $userId,
                'fb_page_id' => $pageData['id'],
                'name' => $pageData['name'],
                'category' => $pageData['category'] ?? '',
                'access_token' => $pageData['access_token']
            ]);

            $postResponse = $this->fb->get("/{$pageData['id']}/posts", $pageData['access_token']);
            $posts = $postResponse->getGraphEdge()->asArray();
            foreach ($posts as $postData) {
                $postId = Post::create([
                    'page_id' => $pageId,
                    'fb_post_id' => $postData['id'],
                    'message' => $postData['message'] ?? '',
                    'created_time' => date('Y-m-d H:i:s', strtotime($postData['created_time'])),
                    'status' => 'published'
                ]);
                $commentResponse = $this->fb->get("/{$postData['id']}/comments", $pageData['access_token']);
                $comments = $commentResponse->getGraphEdge()->asArray();
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

            $convResponse = $this->fb->get("/{$pageData['id']}/conversations", $pageData['access_token']);
            $conversations = $convResponse->getGraphEdge()->asArray();
            foreach ($conversations as $convData) {
                $convId = Conversation::create([
                    'page_id' => $pageId,
                    'fb_conversation_id' => $convData['id'],
                    'customer_psid' => $convData['participants']['data'][0]['id'],
                    'last_message_time' => date('Y-m-d H:i:s', strtotime($convData['updated_time']))
                ]);
                $msgResponse = $this->fb->get("/{$convData['id']}/messages", $pageData['access_token']);
                $messages = $msgResponse->getGraphEdge()->asArray();
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
        set_flash('success', 'Kết nối Facebook thành công.');
        Response::redirect('/fanpages');
    }
}
