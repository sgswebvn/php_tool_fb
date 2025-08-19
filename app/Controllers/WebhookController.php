<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
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
        if ($_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === env('FB_WEBHOOK_TOKEN')) {
            echo $_GET['hub_challenge'];
            exit;
        }
        $payload = json_decode(file_get_contents('php://input'), true);
        $eventId = WebhookEvent::create(['event_type' => $payload['object'] ?? 'unknown', 'payload' => json_encode($payload)]);

        if (isset($payload['entry'])) {
            foreach ($payload['entry'] as $entry) {
                $page = Page::firstWhere('fb_page_id', $entry['id']);
                if (!$page) continue;

                if (isset($entry['changes'])) {
                    foreach ($entry['changes'] as $change) {
                        if ($change['field'] === 'feed') {
                            $postData = $change['value'];
                            $postId = Post::firstWhere('fb_post_id', $postData['post_id']);
                            if ($postId) {
                                Comment::create([
                                    'post_id' => $postId['id'],
                                    'fb_comment_id' => $postData['comment_id'],
                                    'sender_id' => $postData['from']['id'],
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
                            'created_time' => date('Y-m-d H:i:s', strtotime($msg['timestamp'] / 1000)),
                            'direction' => 'in'
                        ]);
                    }
                }
            }
        }

        WebhookEvent::markProcessed($eventId);
        AuditLog::log(['action' => 'webhook_processed', 'event_id' => $eventId]);
        Response::json(['success' => true], 200);
    }
}
