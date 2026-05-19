<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/api_helpers.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Booking.php';

/**
 * ChatbotService — wraps the AI provider with Eventify-scoped tools.
 *
 * Conversation is stateless: caller passes the full message history each turn.
 * Tool calls are executed server-side against existing models, and results
 * are looped back until the model returns a final text reply.
 */
class ChatbotService {

    private string $apiKey;
    private string $model;
    private const MAX_TOOL_TURNS = 6;
    private const MAX_HISTORY    = 20;
    private const AI_ENDPOINT    = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct() {
        $this->apiKey = GROQ_API_KEY;
        $this->model  = GROQ_MODEL;
    }

    /**
     * Main entry. $messages is an array of {role: 'user'|'assistant', content: string}.
     * Returns ['reply' => string, 'proposal' => ?array, 'login_required' => bool].
     */
    public function chat(array $messages, ?int $userId, ?string $role): array {
        if (!$this->apiKey) {
            return ['reply' => 'The chatbot is not configured. Admin: set the AI API key.', 'proposal' => null, 'login_required' => false];
        }

        $isLoggedIn = $userId !== null && $role === 'attendee';
        $systemPrompt = $this->buildSystemPrompt($userId, $role);
        $tools = $this->getToolDefinitions($isLoggedIn);

        // Build OpenAI-format message array (system + trimmed history)
        $apiMessages = [['role' => 'system', 'content' => $systemPrompt]];
        $trimmed = array_slice($messages, -self::MAX_HISTORY);
        foreach ($trimmed as $msg) {
            $role2 = ($msg['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
            $text  = trim((string) ($msg['content'] ?? ''));
            if ($text !== '') {
                $apiMessages[] = ['role' => $role2, 'content' => $text];
            }
        }

        $proposal = null;

        for ($turn = 0; $turn < self::MAX_TOOL_TURNS; $turn++) {
            $resp = $this->callAI($apiMessages, $tools);

            if (isset($resp['error'])) {
                return ['reply' => 'Sorry, I hit an error: ' . $resp['error'], 'proposal' => null, 'login_required' => false];
            }

            $choice  = $resp['choices'][0] ?? null;
            if (!$choice) {
                return ['reply' => 'No response from the assistant. Please try again.', 'proposal' => null, 'login_required' => false];
            }

            $msg2      = $choice['message'];
            $toolCalls = $msg2['tool_calls'] ?? [];
            $content   = $msg2['content'] ?? null;

            // No tool calls — model is done; return its text.
            if (empty($toolCalls)) {
                $reply = trim((string) $content);
                return ['reply' => $reply ?: '...', 'proposal' => $proposal, 'login_required' => false];
            }

            // Append assistant message (with tool_calls) to history
            $apiMessages[] = $msg2;

            // Execute each tool call and append results
            foreach ($toolCalls as $tc) {
                $name   = $tc['function']['name'] ?? '';
                $rawArgs = $tc['function']['arguments'] ?? '{}';
                $args   = is_string($rawArgs) ? (json_decode($rawArgs, true) ?? []) : (array) $rawArgs;
                $result = $this->executeTool($name, $args, $userId, $isLoggedIn);

                if ($name === 'propose_booking' && empty($result['error'])) {
                    $proposal = $result;
                }

                $apiMessages[] = [
                    'role'         => 'tool',
                    'tool_call_id' => $tc['id'],
                    'content'      => json_encode($result),
                ];
            }
        }

        return ['reply' => 'I got stuck in a loop. Please rephrase your request.', 'proposal' => $proposal, 'login_required' => false];
    }

    private function buildSystemPrompt(?int $userId, ?string $role): string {
        $today = date('Y-m-d');
        if ($userId && $role === 'attendee') {
            $userLine = "The user is logged in as an attendee (user_id $userId). They CAN book and cancel events.";
        } elseif ($userId) {
            $userLine = "The user is logged in with role '$role'. They cannot book — bookings are attendee-only. Tell them to log in as an attendee.";
        } else {
            $userLine = "The user is a GUEST (not logged in). They can browse and ask about events, but cannot book. If they want to book, tell them to log in.";
        }

        return "You are the Eventify assistant for an event booking website. Today is $today.\n\n"
            . "$userLine\n\n"
            . "STRICT SCOPE: You ONLY help with these topics on this website:\n"
            . "  - Searching/browsing events (by name, category, city, date)\n"
            . "  - Showing event details (price, venue, seats, date)\n"
            . "  - Listing available event categories\n"
            . "  - For logged-in attendees: viewing their bookings, creating bookings, cancelling bookings\n\n"
            . "REFUSE everything else (general knowledge, jokes, news, weather, coding help, math, other websites). "
            . "Reply briefly: 'I can only help with events and bookings on Eventify.' and suggest a relevant action.\n\n"
            . "RULES:\n"
            . "  1. Always use tools for real data — never invent event names, prices, dates, or booking refs.\n"
            . "  2. To book: call propose_booking FIRST, present the summary (event title, date, qty, total), and wait for the user to explicitly confirm ('yes', 'confirm', 'book it'). Only then call create_booking.\n"
            . "  3. To cancel: show the booking_ref and ask for confirmation before calling cancel_booking.\n"
            . "  4. Be concise. Format event lists as short bullet points with title, date, venue, price.\n"
            . "  5. If a tool returns an error, explain it plainly and suggest what the user can do.";
    }

    /** OpenAI-format tool declarations. Booking tools are omitted for guests. */
    private function getToolDefinitions(bool $isLoggedIn): array {
        $functions = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_events',
                    'description' => 'Search published events. All filters are optional.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query'    => ['type' => 'string', 'description' => 'Free-text search across title/description/venue.'],
                            'category' => ['type' => 'string', 'description' => 'Category name, e.g. "Concert", "Football".'],
                            'limit'    => ['type' => 'integer', 'description' => 'Max results (default 10, max 20).'],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_event_detail',
                    'description' => 'Get full details for one event by id.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'event_id' => ['type' => 'integer', 'description' => 'The event id from search_events results.'],
                        ],
                        'required' => ['event_id'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'list_categories',
                    'description' => 'List all event categories available on Eventify.',
                    'parameters' => ['type' => 'object', 'properties' => new stdClass()],
                ],
            ],
        ];

        if ($isLoggedIn) {
            $functions[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'get_my_bookings',
                    'description' => "Fetch the current user's bookings.",
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'type' => ['type' => 'string', 'description' => "'upcoming' or 'past'. Default 'upcoming'."],
                        ],
                    ],
                ],
            ];
            $functions[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'propose_booking',
                    'description' => 'Validate seat availability and compute total. Does NOT book yet.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'event_id' => ['type' => 'integer'],
                            'quantity' => ['type' => 'integer', 'description' => '1 to 5.'],
                        ],
                        'required' => ['event_id', 'quantity'],
                    ],
                ],
            ];
            $functions[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'create_booking',
                    'description' => 'Actually create the booking. Only call AFTER the user has explicitly confirmed.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'event_id' => ['type' => 'integer'],
                            'quantity' => ['type' => 'integer'],
                        ],
                        'required' => ['event_id', 'quantity'],
                    ],
                ],
            ];
            $functions[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'cancel_booking',
                    'description' => 'Cancel a confirmed booking.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'booking_id' => ['type' => 'integer', 'description' => 'The booking id from get_my_bookings.'],
                        ],
                        'required' => ['booking_id'],
                    ],
                ],
            ];
        }

        return $functions;
    }

    /** Dispatch a tool call to the right handler. Always returns an array. */
    private function executeTool(string $name, array $args, ?int $userId, bool $isLoggedIn): array {
        try {
            switch ($name) {
                case 'search_events':    return $this->toolSearchEvents($args);
                case 'get_event_detail': return $this->toolGetEventDetail($args);
                case 'list_categories':  return $this->toolListCategories();
                case 'get_my_bookings':
                    if (!$isLoggedIn) return ['error' => 'login_required'];
                    return $this->toolGetMyBookings($args, $userId);
                case 'propose_booking':
                    if (!$isLoggedIn) return ['error' => 'login_required'];
                    return $this->toolProposeBooking($args);
                case 'create_booking':
                    if (!$isLoggedIn) return ['error' => 'login_required'];
                    return $this->toolCreateBooking($args, $userId);
                case 'cancel_booking':
                    if (!$isLoggedIn) return ['error' => 'login_required'];
                    return $this->toolCancelBooking($args, $userId);
                default:
                    return ['error' => "Unknown tool: $name"];
            }
        } catch (Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // ── Tool implementations ──────────────────────────────────────

    private function toolSearchEvents(array $args): array {
        $query    = trim((string) ($args['query'] ?? ''));
        $category = trim((string) ($args['category'] ?? ''));
        $limit    = max(1, min(20, (int) ($args['limit'] ?? 10)));

        $eventModel = new Event();
        $events = $eventModel->getApproved($query, $category, $limit, 0);
        $total  = $eventModel->countApproved($query, $category);

        $slim = array_map(fn($e) => [
            'event_id'        => (int) $e['event_id'],
            'title'           => $e['title'],
            'category'        => $e['category'],
            'venue'           => $e['venue'],
            'city'            => $e['city'],
            'date'            => $e['event_date'],
            'time'            => $e['event_time'],
            'price_npr'       => (float) $e['ticket_price'],
            'available_seats' => (int) $e['available_seats'],
        ], $events);

        return ['count' => count($slim), 'total_matching' => $total, 'events' => $slim];
    }

    private function toolGetEventDetail(array $args): array {
        $id = (int) ($args['event_id'] ?? 0);
        if (!$id) return ['error' => 'event_id is required'];

        $event = (new Event())->findById($id);
        if (!$event || $event['status'] !== 'published') {
            return ['error' => 'Event not found or not published.'];
        }
        return [
            'event_id'        => (int) $event['event_id'],
            'title'           => $event['title'],
            'description'     => $event['description'],
            'category'        => $event['category'],
            'venue'           => $event['venue'],
            'city'            => $event['city'],
            'date'            => $event['event_date'],
            'time'            => $event['event_time'],
            'price_npr'       => (float) $event['ticket_price'],
            'available_seats' => (int) $event['available_seats'],
            'organizer'       => $event['organizer_name'] ?? null,
        ];
    }

    private function toolListCategories(): array {
        $stmt = getDB()->query('SELECT id, name FROM event_categories ORDER BY name ASC');
        return ['categories' => $stmt->fetchAll()];
    }

    private function toolGetMyBookings(array $args, int $userId): array {
        $type = ($args['type'] ?? 'upcoming') === 'past' ? 'past' : 'upcoming';
        $rows = (new Booking())->getByAttendee($userId, $type);
        $slim = array_map(fn($b) => [
            'booking_id'  => (int) $b['booking_id'],
            'booking_ref' => $b['booking_ref'],
            'event_title' => $b['title'],
            'date'        => $b['event_date'],
            'venue'       => $b['venue'],
            'quantity'    => (int) $b['quantity'],
            'total_npr'   => (float) $b['total_amount'],
            'status'      => $b['status'],
        ], $rows);
        return ['type' => $type, 'count' => count($slim), 'bookings' => $slim];
    }

    private function toolProposeBooking(array $args): array {
        $eventId = (int) ($args['event_id'] ?? 0);
        $qty     = max(1, min(5, (int) ($args['quantity'] ?? 1)));
        if (!$eventId) return ['error' => 'event_id is required'];

        $event = (new Event())->findById($eventId);
        if (!$event || $event['status'] !== 'published') return ['error' => 'Event not available.'];
        if ($event['available_seats'] < $qty) {
            return ['error' => "Only {$event['available_seats']} seats available."];
        }
        $total = (float) $event['ticket_price'] * $qty;
        return [
            'event_id'   => (int) $event['event_id'],
            'title'      => $event['title'],
            'date'       => $event['event_date'],
            'venue'      => $event['venue'],
            'quantity'   => $qty,
            'unit_price' => (float) $event['ticket_price'],
            'total_npr'  => $total,
            'note'       => 'Awaiting user confirmation. Do NOT call create_booking until the user explicitly confirms.',
        ];
    }

    private function toolCreateBooking(array $args, int $userId): array {
        $eventId = (int) ($args['event_id'] ?? 0);
        $qty     = max(1, min(5, (int) ($args['quantity'] ?? 1)));
        if (!$eventId) return ['error' => 'event_id is required'];

        $eventModel = new Event();
        $event = $eventModel->findById($eventId);
        if (!$event || $event['status'] !== 'published') return ['error' => 'Event not available.'];
        if ($event['available_seats'] < $qty) {
            return ['error' => "Only {$event['available_seats']} seats available."];
        }

        $bookingModel = new Booking();
        $total      = (float) $event['ticket_price'] * $qty;
        $bookingRef = generateBookingRef();
        $bookingId  = $bookingModel->create($bookingRef, $userId, $eventId, $qty, $total);
        $bookingModel->confirm($bookingId);
        $eventModel->decrementSeats($eventId, $qty);
        $bookingModel->createPayment($bookingId, 'CHATBOT-' . $bookingRef, $total);

        return [
            'booking_id'  => (int) $bookingId,
            'booking_ref' => $bookingRef,
            'event_title' => $event['title'],
            'date'        => $event['event_date'],
            'quantity'    => $qty,
            'total_npr'   => $total,
            'status'      => 'confirmed',
        ];
    }

    private function toolCancelBooking(array $args, int $userId): array {
        $bookingId = (int) ($args['booking_id'] ?? 0);
        if (!$bookingId) return ['error' => 'booking_id is required'];
        $ok = (new Booking())->cancel($bookingId, $userId);
        return $ok
            ? ['booking_id' => $bookingId, 'status' => 'cancelled']
            : ['error' => 'Could not cancel — booking not found, not yours, or already cancelled.'];
    }

    /** POST to the AI chat completions endpoint. */
    private function callAI(array $messages, array $tools): array {
        $payload = [
            'model'       => $this->model,
            'messages'    => $messages,
            'tools'       => $tools,
            'temperature' => 0.4,
        ];

        $ch = curl_init(self::AI_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
        ]);
        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($body === false) return ['error' => "Network error: $curlErr"];
        $decoded = json_decode($body, true);
        if (!is_array($decoded)) return ['error' => 'Invalid response from AI.'];
        if ($httpCode >= 400) {
            $msg = $decoded['error']['message'] ?? "HTTP $httpCode";
            return ['error' => $msg];
        }
        return $decoded;
    }
}
