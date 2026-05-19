<?php
/**
 * Search API
 *
 * GET  ?q=X           — Autocomplete event titles (public, min 2 chars)
 * GET  ?q=X&full=1    — Full search with event details (public)
 */

$method = getRequestMethod();

if ($method !== 'GET') {
    jsonResponse(405, ['error' => 'Method not allowed. Use GET.']);
}

$q     = trim($_GET['q'] ?? '');
$full  = !empty($_GET['full']);
$limit = min(20, max(1, (int) ($_GET['limit'] ?? 8)));

if (strlen($q) < 2) {
    jsonResponse(200, ['results' => [], 'query' => $q]);
}

$eventModel = new Event();

if ($full) {
    // Full search returns complete event objects
    $events = $eventModel->getApproved($q, '', $limit, 0);
    jsonResponse(200, [
        'query'   => $q,
        'count'   => count($events),
        'results' => $events,
    ]);
} else {
    // Autocomplete returns just titles and IDs
    $results = $eventModel->searchTitles($q, $limit);
    jsonResponse(200, [
        'query'   => $q,
        'count'   => count($results),
        'results' => $results,
    ]);
}
