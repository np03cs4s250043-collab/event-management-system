<?php
require_once __DIR__ . '/../models/Event.php';

// Controller for handling search-related requests
class SearchController {

    // Autocomplete search for event titles
    public function autocomplete(): void {

    // Return response as JSON
        header('Content-Type: application/json');

        // Get search query from URL
        $q = trim($_GET['q'] ?? '');

        // If query is too short, return empty results
        if (strlen($q) < 2) { echo json_encode(['results' => []]); exit; }

        // Create Event model object
        $eventModel = new Event();

        // Search matching event titles (max 8 results)
        $results = $eventModel->searchTitles($q, 8);

        // Return results as JSON
        echo json_encode(['results' => $results]);
        exit;
    }

    // Filter events by category and search keyword
    public function filterEvents(): void {

        // Return response as JSON
        header('Content-Type: application/json');

        // Get category and search keyword from URL
        $category = trim($_GET['category'] ?? '');
        $search = trim($_GET['search'] ?? '');

        // Create Event model object
        $eventModel = new Event();

        // Get approved events based on filters
        $events = $eventModel->getApproved($search, $category, 12, 0);
        
        // Return filtered events as JSON
        echo json_encode(['events' => $events]);
        exit;
    }
}
