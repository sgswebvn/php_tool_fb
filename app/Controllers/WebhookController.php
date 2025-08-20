<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Models\WebhookEvent;
use App\Models\Page;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AuditLog;

class WebhookController extends Controller
{
    public function handle()
    {
        $mode = $_GET['hub_mode'] ?? null;
        $token = $_GET['hub_verify_token'] ?? null;
        $challenge = $_GET['hub_challenge'] ?? null;

        if ($mode === 'subscribe' && $token === env('FB_WEBHOOK_TOKEN')) {
            echo $challenge;
            exit;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        $eventId = WebhookEvent::create([
            'page_id' => $payload['entry'][0]['id'] ?? null,
            'event_type' => $payload['object'] ?? 'unknown',
            'payload' => json_encode($payload),
            'processed' => 0
        ]);

        if (isset($payload['entry'])) {
            foreach ($payload['entry'] as $entry) {
                $page = Page::firstWhere('fb_page_id', $entry['id']);
                if (!$page) continue;

                if (isset($entry['changes'])) {
                    foreach ($entry['changes'] as $change) {
                        if ($change['field'] === 'feed') {
                            $postData = $change['value'];
                            $post = Post::firstWhere('fb_post_id', $postData['post_id']);
                            if ($post) {
                                Comment::create([
                                    'post_id' => $post['id'],
                                    'fb_comment_id' => $postData['comment_id'],
                                    'parent_comment_id' => $postData['parent_comment_id'] ?? null,
                                    'from_id' => $postData['from']['id'],
                                    'from_name' => $postData['from']['name'] ?? '',
                                    'message' => $postData['message'] ?? '',
                                    'created_time' => date('Y-m-d H:i:s', strtotime($postData['created_time']))
                                ]);
                            }
                        }
                    }
                }

                if (isset($entry['messaging'])) {
                    foreach ($entry['messaging'] as $msg) {
                        $conv = Conversation::firstWhere('fb_conversation_id', $msg['conversation']['id']) ?? Conversation::create([
                            'page_id' => $page['id'],
                            'fb_conversation_id' => $msg['conversation']['id'],
                            'customer_psid' => $msg['sender']['id']
                        ]);
                        Message::create([
                            'conversation_id' => $conv['id'],
                            'fb_message_id' => $msg['message']['mid'],
                            'sender_id' => $msg['sender']['id'],
                            'recipient_id' => $msg['recipient']['id'],
                            'message' => $msg['message']['text'] ?? '',
                            'attachments' => json_encode($msg['message']['attachments'] ?? []),
                            'created_time' => date('Y-m-d H:i:s', $msg['timestamp'] / 1000),
                            'direction' => 'in'
                        ]);
                    }
                }
            }
        }

        WebhookEvent::update($eventId, ['processed' => 1]);
        AuditLog::log(['action' => 'webhook_processed', 'event_id' => $eventId]);
        Response::redirect('/fanpages'); // Hoặc không redirect, tùy cấu hình
    }
}
