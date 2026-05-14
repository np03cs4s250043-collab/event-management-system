<?php
/**
 * Chatbot API
 *
 * POST  ?resource=chatbot   { messages: [{role: 'user'|'assistant', content: string}, ...] }
 *
 * Uses session auth (the chatbot widget runs on logged-in pages with $_SESSION).
 * Returns: { reply: string, proposal?: {...}, login_required?: bool }
 */

// The site uses sessions; the chatbot acts on behalf of the logged-in user.
// api/index.php does not start the session (it's a Bearer-token API), so we do it here.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../core/ChatbotService.php';

$method = getRequestMethod();
if ($method !== 'POST') {
    jsonResponse(405, ['error' => 'Method not allowed. Use POST.']);
}

$input    = jsonInput();
$messages = $input['messages'] ?? [];
if (!is_array($messages) || empty($messages)) {
    jsonResponse(422, ['error' => 'messages array is required.']);
}

$userId = $_SESSION['user_id'] ?? null;
$role   = $_SESSION['role'] ?? null;

$service = new ChatbotService();
$result  = $service->chat($messages, $userId ? (int) $userId : null, $role);

jsonResponse(200, $result);
