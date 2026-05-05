<?php
// controllers/EventController.php
// Handles event-related actions like create and cancel
declare(strict_types=1);

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../core/session_helper.php';
require_once __DIR__ . '/../core/csrf_helper.php';
require_once __DIR__ . '/../core/api_helpers.php';
require_once __DIR__ . '/../models/Event.php';

class EventController
{
    private Event $eventModel;

    public function __construct()
    {
        $this->eventModel = new Event();
    }

    // Show homepage with limited events
    public function home(): void
    {
        $pageTitle = 'Home';
        $events = $this->eventModel->getApproved('', '', 6, 0);
        $this->render('events/index.php', compact('pageTitle', 'events'));
    }

    // Browse events with search
    public function browse(): void
    {
        $search = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');
        $perPage = 9;
        $currentPage = max(1, (int) ($_GET['p'] ?? 1));
        $offset = ($currentPage - 1) * $perPage;

        // Fetch events
        $events = $this->eventModel->getApproved($search, $category, $perPage, $offset);
        $total = $this->eventModel->countApproved($search, $category);
        
        // Calculate total pages
        $totalPages = max(1, (int) ceil($total / $perPage));

        $pg = [
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total' => $total,
            'per_page' => $perPage,
        ];

        $pageTitle = 'Browse Events';
        $this->render('events/detail.php', compact('pageTitle', 'events', 'search', 'category', 'pg'));
    }

    // Show single event details
    public function detail(): void
    {
        $eventId = (int) ($_GET['id'] ?? 0);
        if ($eventId <= 0) {
            $this->redirectPage('events');
        }

        $event = $this->eventModel->findById($eventId);
        if (!$event) {
            $this->redirectPage('events');
        }

        // Get rating and reviews
        $avgRating = $this->eventModel->getAvgRating($eventId);
        $reviews = $this->eventModel->getReviews($eventId);

        $maxCapacity = max(1, (int) ($event['max_capacity'] ?? 1));
        $availableSeats = max(0, (int) ($event['available_seats'] ?? 0));
        $booked = max(0, $maxCapacity - $availableSeats);
        $seatPercent = (int) round(($booked / $maxCapacity) * 100);
        $seatPercent = max(0, min(100, $seatPercent));
        $maxBookable = max(1, min(10, $availableSeats));

        $pageTitle = $event['title'] ?? 'Event Details';
        $this->render(
            'events/detail.php',
            compact('pageTitle', 'event', 'avgRating', 'reviews', 'seatPercent', 'maxBookable')
        );
    }

    // Organizer dashboard
    public function organizerDashboard(): void
    {
        requireRole('organizer', 'admin');
        $events = $this->eventModel->getByOrganizer((int) currentUserId());
        $pageTitle = 'Organizer Dashboard';
        $this->render('organizer/dashboard.php', compact('pageTitle', 'events'));
    }

     // Organizer's own events
    public function organizerHome(): void
    {
        requireRole('organizer', 'admin');
        $events = $this->eventModel->getByOrganizer((int) currentUserId());
        $pageTitle = 'My Events';
        $this->render('organizer/home.php', compact('pageTitle', 'events'));
    }

    // Show create event page
    public function create(): void
    {
        requireRole('organizer', 'admin');
        $pageTitle = 'Create Event';
        $this->render('events/create.php', compact('pageTitle'));
    }

    public function edit(): void
    {
        requireRole('organizer', 'admin');
        $this->redirectPage('organizer/dashboard');
    }

    public function deleteOrganizer(): void
    {
        requireRole('organizer', 'admin');
        $this->redirectPage('organizer/dashboard');
    }

    // Admin dashboard
    public function adminDashboard(): void
    {
        requireRole('admin');
        $pageTitle = 'Admin Dashboard';
        $this->render('admin/dashboard.php', compact('pageTitle'));
    }

    // Admin manage events
    public function adminEvents(): void
    {
        requireRole('admin');
        $events = $this->eventModel->getAllAdmin();
        $pageTitle = 'Manage Events';
        $this->render('admin/events.php', compact('pageTitle', 'events'));
    }

    // Admin manage users
    public function adminUsers(): void
    {
        requireRole('admin');
        $pageTitle = 'Manage Users';
        $this->render('admin/users.php', compact('pageTitle'));
    }

  public function editAdmin(): void {
    requireRole('admin');
    $pageTitle = 'Edit Event';
    $hideNav = true;

    $eventModel = new Event();
    $id = (int)($_GET['id'] ?? 0);
    $event = $eventModel->findById($id);

    if (!$event) {
        setFlash('error', 'Event not found.');
        redirect(APP_URL . '/index.php?page=admin/events');
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!validateCSRF()) {
            setFlash('error', 'Invalid request.');
            redirect(APP_URL . '/index.php?page=admin/edit_event&id=' . $id);
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'category' => $_POST['category'] ?? '',
            'event_date' => $_POST['event_date'] ?? '',
            'event_time' => $_POST['event_time'] ?? '',
            'venue' => trim($_POST['venue'] ?? ''),
            'max_capacity' => (int)($_POST['max_capacity'] ?? 0),
            'ticket_price' => (float)($_POST['ticket_price'] ?? 0),
            'cover_image' => $event['cover_image'] ?? null,
        ];

        if (!$data['title']) $errors[] = 'Title is required.';
        if (!$data['description']) $errors[] = 'Description is required.';
        if (!in_array($data['category'], ['Concert','Conference','Workshop','Webinar','Sports','Festival','Exhibition','Networking','Music Events','Football','Cricket'])) $errors[] = 'Invalid category.';
        if (!$data['event_date']) $errors[] = 'Date is required.';
        if (!$data['event_time']) $errors[] = 'Time is required.';
        if (!$data['venue']) $errors[] = 'Venue is required.';
        if ($data['max_capacity'] < 1) $errors[] = 'Capacity must be at least 1.';
        if ($data['ticket_price'] <= 0) $errors[] = 'Price must be greater than 0.';

        if (!empty($_FILES['cover_image']['name'])) {
            $uploadError = '';
            $img = uploadImage($_FILES['cover_image'], $uploadError);
            if ($img) {
                $data['cover_image'] = $img;
            } else {
                $errors[] = $uploadError;
            }
        }

        if (empty($errors)) {
            $eventModel->update($id, $data);
            setFlash('success', 'Event updated successfully.');
            redirect(APP_URL . '/index.php?page=admin/events');
        }
    }

    $sidebarLinks = [
        ['url' => APP_URL . '/index.php?page=admin/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['url' => APP_URL . '/index.php?page=admin/users', 'icon' => 'group', 'label' => 'Manage Users'],
        ['url' => APP_URL . '/index.php?page=admin/events', 'icon' => 'event', 'label' => 'Manage Events', 'active' => true],
        ['url' => APP_URL . '/index.php?page=admin/commission', 'icon' => 'handshake', 'label' => 'Commissions'],
        ['url' => APP_URL . '/index.php?page=admin/bookings', 'icon' => 'confirmation_number', 'label' => 'All Bookings'],
        ['url' => APP_URL . '/index.php?page=admin/revenue', 'icon' => 'bar_chart', 'label' => 'Revenue Report'],
    ];

    require_once __DIR__ . '/../views/layouts/header.php';
    require_once __DIR__ . '/../views/layouts/sidebar_admin.php';
    require_once __DIR__ . '/../views/events/create.php';
    require_once __DIR__ . '/../views/layouts/footer.php';
}

    // Approve event
    public function approve(): void
    {
        requireRole('admin');
        $eventId = (int) ($_POST['event_id'] ?? $_GET['event_id'] ?? 0);
        if ($eventId > 0) {
            $this->eventModel->setStatus($eventId, 'published');
        }
        $this->redirectPage('admin/events');
    }

    // Delete event (admin)
    public function deleteAdmin(): void
    {
        requireRole('admin');
        $eventId = (int) ($_POST['event_id'] ?? $_GET['event_id'] ?? 0);
        if ($eventId > 0) {
            $this->eventModel->delete($eventId);
        }
        $this->redirectPage('admin/events');
    }

    // Enable/disable user
    public function toggleUser(): void
    {
        requireRole('admin');
        $userId = (int) ($_POST['user_id'] ?? $_GET['user_id'] ?? 0);
        if ($userId > 0) {
            $db = getDB();
            $stmt = $db->prepare('UPDATE users SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = ?');
            $stmt->execute([$userId]);
        }
        $this->redirectPage('admin/users');
    }

    // Load view files
    private function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/' . $view;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    // Redirect to another page
    private function redirectPage(string $page): never
    {
        header('Location: ' . BASE_PATH . '/index.php?page=' . $page);
        exit;
    }
}