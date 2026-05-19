<?php
require_once __DIR__ . '/../models/Event.php';

class SearchController {

    public function autocomplete(): void {
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) { echo json_encode(['results' => []]); exit; }

        $eventModel = new Event();
        $results = $eventModel->searchTitles($q, 8);
        echo json_encode(['results' => $results]);
        exit;
    }

    public function filterEvents(): void {
        header('Content-Type: application/json');
        $category = trim($_GET['category'] ?? '');
        $search = trim($_GET['search'] ?? '');

        $eventModel = new Event();
        $events = $eventModel->getApproved($search, $category, 12, 0);
        echo json_encode(['events' => $events]);
        exit;
    }
}
