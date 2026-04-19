<?php
// controllers/EventController.php
// Handles event-related actions like create and cancel
declare(strict_types=1);

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../core/csrf_helper.php';
require_once __DIR__ . '/../core/session_helper.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Ticket.php';

// Ensure user is logged in
requireLogin();

// Get action from request (create or cancel)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Call correct function based on action
match($action) {
    'create' => handleCreate(),
    'cancel' => handleCancel(),
    default  => redirect('events'),
};

// Create new event
function handleCreate(): void
{
    requireRole('organizer', 'admin');
    validateCsrfToken($_POST['csrf_token'] ?? '');

    // Required fields check
    $required = ['title', 'description', 'venue', 'city', 'date_start', 'date_end', 'capacity', 'category_id'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            setFlash('error', "Field '$field' is required.");
            redirect('event.create');
        }
    }

    // Handle cover image upload
    $coverImage = null;
    if (!empty($_FILES['cover_image']['tmp_name'])) {
        $allowed   = ['image/jpeg', 'image/png', 'image/webp'];
        $mimeType  = mime_content_type($_FILES['cover_image']['tmp_name']);
        if (!in_array($mimeType, $allowed, true)) {
            setFlash('error', 'Invalid image type. Use JPG, PNG, or WebP.');
            redirect('event.create');
        }
        $ext        = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $filename   = bin2hex(random_bytes(8)) . '.' . strtolower($ext);
        $uploadDir  = __DIR__ . '/../public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . $filename);
        $coverImage = $filename;
    }

    // Save event to database
    $eventModel = new Event();
    $eventId    = $eventModel->create([
        'title'       => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'venue'       => trim($_POST['venue']),
        'city'        => trim($_POST['city']),
        'date_start'  => $_POST['date_start'],
        'date_end'    => $_POST['date_end'],
        'capacity'    => (int)$_POST['capacity'],
        'category_id' => (int)$_POST['category_id'],
        'cover_image' => $coverImage,
        'status'      => in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'draft',
    ], currentUserId());

    if (!empty($_POST['ticket_name']) && isset($_POST['ticket_price'])) {
        $ticketModel = new Ticket();
        $ticketModel->create([
            'event_id' => $eventId,
            'name'     => trim($_POST['ticket_name']),
            'price'    => (float)$_POST['ticket_price'],
            'quantity' => (int)($_POST['ticket_quantity'] ?? 50),
        ]);
    }

    setFlash('success', 'Event created successfully!');
    redirect('organizer.dashboard');
}

function handleCancel(): void
{
    requireRole('organizer', 'admin');
    validateCsrfToken($_POST['csrf_token'] ?? '');

    $eventId = (int)($_POST['event_id'] ?? 0);
    if (!$eventId) { redirect('organizer.dashboard'); }

    $eventModel = new Event();
    $event      = $eventModel->getById($eventId);

    // Organizers can only cancel their own events; admins can cancel any
    if (!$event || (currentRole() === 'organizer' && $event['organizer_id'] !== currentUserId())) {
        setFlash('error', 'Not authorised to cancel this event.');
        redirect('organizer.dashboard');
    }

    // Cancel event in database
    $eventModel->cancel($eventId);
    setFlash('success', 'Event cancelled.');
    redirect(currentRole() === 'admin' ? 'admin.events' : 'organizer.dashboard');
}

function redirect(string $page): never
{
    header('Location: ' . BASE_PATH . "/index.php?page=$page");
    exit;
}
