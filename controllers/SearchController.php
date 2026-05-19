<?php
require_once __DIR__ . '/../models/Event.php';

// Autocomplete search method
class SearchController {

    public function autocomplete(): void {

        // Set response type to JSON
        header('Content-Type: application/json');

        // Get search query
        $q = trim($_GET['q'] ?? '');

        // Return empty results if query is too short
        if (strlen($q) < 2) { echo json_encode(['results' => []]); exit; }

        // Create event model
        $eventModel = new Event();

        // Search event titles
        $results = $eventModel->searchTitles($q, 8);

        // Return search results
        echo json_encode(['results' => $results]);
        exit;
    }

    // Filter events by category and search text
    public function filterEvents(): void {

        // Set response type to JSON
        header('Content-Type: application/json');

        // Get filter values
        $category = trim($_GET['category'] ?? '');
        $search = trim($_GET['search'] ?? '');

        // Create event model
        $eventModel = new Event();

        // Get approved filtered events
        $events = $eventModel->getApproved($search, $category, 12, 0);

        // Return event list
        echo json_encode(['events' => $events]);
        exit;
    }
}
