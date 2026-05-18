<?php
/**
 * Categories API
 *
 * GET   — List all event categories (public, from database)
 */

$method = getRequestMethod();

if ($method !== 'GET') {
    jsonResponse(405, ['error' => 'Method not allowed. Use GET.']);
}

$db   = getDB();
$stmt = $db->query('SELECT id, name, slug, created_at FROM event_categories ORDER BY name ASC');
$categories = $stmt->fetchAll();

jsonResponse(200, ['categories' => $categories]);
