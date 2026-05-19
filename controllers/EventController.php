<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Booking.php';

class EventController {

    public function home(): void {
        $pageTitle = 'Discover Events Near You';
        $currentPage = 'home';
        $eventModel = new Event();
        $events = $eventModel->getApproved('', '', 6, 0);

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/events/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function browse(): void {
        $pageTitle = 'Browse Events';
        $currentPage = 'events';
        $eventModel = new Event();
        $search = trim($_GET['search'] ?? '');
        $category = $_GET['category'] ?? '';
        $page = max(1, (int)($_GET['p'] ?? 1));

        $total = $eventModel->countApproved($search, $category);
        $pg = paginate($total, EVENTS_PER_PAGE, $page);
        $events = $eventModel->getApproved($search, $category, $pg['per_page'], $pg['offset']);

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/events/detail.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function detail(): void {
        $pageTitle = 'Event Details';
        $eventModel = new Event();
        $id = (int)($_GET['id'] ?? 0);
        $event = $eventModel->findById($id);
        if (!$event || $event['status'] !== 'published') {
            setFlash('error', 'Event not found.');
            redirect(APP_URL . '/index.php?page=events');
        }
        $avgRating = $eventModel->getAvgRating($id);
        $reviews = $eventModel->getReviews($id);
        $seatPercent = $event['max_capacity'] > 0 ? round(($event['max_capacity'] - $event['available_seats']) / $event['max_capacity'] * 100) : 0;
        $maxBookable = min(5, $event['available_seats']);

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/events/detail.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function create(): void {
        requireRole('organizer');
        $pageTitle = 'Create Event';
        $hideNav = true;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRF()) { setFlash('error', 'Invalid request.'); redirect(APP_URL . '/index.php?page=organizer/create'); }
            $data = [
                'organizer_id' => currentUserId(),
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'category' => $_POST['category'] ?? '',
                'event_date' => $_POST['event_date'] ?? '',
                'event_time' => $_POST['event_time'] ?? '',
                'venue' => trim($_POST['venue'] ?? ''),
                'max_capacity' => (int)($_POST['max_capacity'] ?? 0),
                'ticket_price' => (float)($_POST['ticket_price'] ?? 0),
                'cover_image' => null,
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
                if ($img) { $data['cover_image'] = $img; }
                else { $errors[] = $uploadError; }
            }

            if (empty($errors)) {
                $eventModel = new Event();
                $eventModel->create($data);
                setFlash('success', 'Event created! It will be visible after admin approval.');
                redirect(APP_URL . '/index.php?page=organizer/dashboard');
            }
        }

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=organizer/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['url' => APP_URL . '/index.php?page=organizer/create', 'icon' => 'add_circle', 'label' => 'Create Event', 'active' => true],
            ['url' => APP_URL . '/index.php?page=organizer/events', 'icon' => 'event', 'label' => 'My Events'],
            ['url' => APP_URL . '/index.php', 'icon' => 'explore', 'label' => 'Browse Events'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_organizer.php';
        require_once __DIR__ . '/../views/events/create.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function edit(): void {
        requireRole('organizer');
        $pageTitle = 'Edit Event';
        $hideNav = true;
        $eventModel = new Event();
        $id = (int)($_GET['id'] ?? 0);
        $event = $eventModel->findById($id);
        if (!$event || $event['organizer_id'] != currentUserId()) {
            setFlash('error', 'Event not found.');
            redirect(APP_URL . '/index.php?page=organizer/dashboard');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRF()) { setFlash('error', 'Invalid request.'); redirect(APP_URL . '/index.php?page=organizer/edit&id=' . $id); }
            $data = [
                'title' => trim($_POST['title'] ?? ''), 'description' => trim($_POST['description'] ?? ''),
                'category' => $_POST['category'] ?? '', 'event_date' => $_POST['event_date'] ?? '',
                'event_time' => $_POST['event_time'] ?? '', 'venue' => trim($_POST['venue'] ?? ''),
                'max_capacity' => (int)($_POST['max_capacity'] ?? 0), 'ticket_price' => (float)($_POST['ticket_price'] ?? 0),
                'cover_image' => null,
            ];
            if (!$data['title']) $errors[] = 'Title is required.';
            if (!empty($_FILES['cover_image']['name'])) {
                $uploadError = '';
                $img = uploadImage($_FILES['cover_image'], $uploadError);
                if ($img) $data['cover_image'] = $img; else $errors[] = $uploadError;
            }
            if (empty($errors)) {
                $eventModel->update($id, $data);
                setFlash('success', 'Event updated.');
                redirect(APP_URL . '/index.php?page=organizer/dashboard');
            }
        }

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=organizer/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['url' => APP_URL . '/index.php?page=organizer/create', 'icon' => 'add_circle', 'label' => 'Create Event'],
            ['url' => APP_URL . '/index.php?page=organizer/events', 'icon' => 'event', 'label' => 'My Events', 'active' => true],
            ['url' => APP_URL . '/index.php', 'icon' => 'explore', 'label' => 'Browse Events'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_organizer.php';
        require_once __DIR__ . '/../views/events/create.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function deleteOrganizer(): void {
        requireRole('organizer');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF()) {
            $id = (int)($_POST['event_id'] ?? 0);
            $eventModel = new Event();
            $event = $eventModel->findById($id);
            if ($event && $event['organizer_id'] == currentUserId()) {
                $eventModel->delete($id);
                setFlash('success', 'Event deleted.');
            }
        }
        redirect(APP_URL . '/index.php?page=organizer/dashboard');
    }

    public function approve(): void {
        requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF()) {
            $id = (int)($_POST['event_id'] ?? 0);
            $action = $_POST['action'] ?? '';
            $eventModel = new Event();
            if ($action === 'approve') {
                $eventModel->setStatus($id, 'published');
                setFlash('success', 'Event approved.');
            } elseif ($action === 'reject') {
                $eventModel->setStatus($id, 'cancelled');
                setFlash('success', 'Event rejected.');
            }
        }
        redirect($_SERVER['HTTP_REFERER'] ?? APP_URL . '/index.php?page=admin/dashboard');
    }

    public function deleteAdmin(): void {
        requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF()) {
            (new Event())->delete((int)($_POST['event_id'] ?? 0));
            setFlash('success', 'Event deleted.');
        }
        redirect($_SERVER['HTTP_REFERER'] ?? APP_URL . '/index.php?page=admin/events');
    }

    public function toggleUser(): void {
        requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF()) {
            $id = (int)($_POST['user_id'] ?? 0);
            require_once __DIR__ . '/../models/User.php';
            (new User())->toggleStatus($id);
            setFlash('success', 'User status updated.');
        }
        redirect(APP_URL . '/index.php?page=admin/users');
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
    public function adminDashboard(): void
    {
        requireRole('admin');
        $pageTitle = 'Admin Dashboard';
        $hideNav = true;
        $bookingModel = new Booking();
        $eventModel = new Event();
        $stats = $bookingModel->adminStats();
        $pending = $eventModel->getPending();

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=admin/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'active' => true],
            ['url' => APP_URL . '/index.php?page=admin/users', 'icon' => 'group', 'label' => 'Manage Users'],
            ['url' => APP_URL . '/index.php?page=admin/events', 'icon' => 'event', 'label' => 'Manage Events'],
            ['url' => APP_URL . '/index.php?page=admin/bookings', 'icon' => 'confirmation_number', 'label' => 'All Bookings'],
            ['url' => APP_URL . '/index.php?page=admin/revenue', 'icon' => 'bar_chart', 'label' => 'Revenue Report'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_admin.php';
        require_once __DIR__ . '/../views/admin/dashboard.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function adminEvents(): void {
        requireRole('admin');
        $pageTitle = 'Manage Events';
        $hideNav = true;
        $eventModel = new Event();
        $search = trim($_GET['search'] ?? '');
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = max(1, (int)($_GET['p'] ?? 1));
        $total = $eventModel->countAllAdmin($search, $category, $status);
        $pg = paginate($total, 20, $page);
        $events = $eventModel->getAllAdmin($search, $category, $status, $pg['per_page'], $pg['offset']);

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=admin/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['url' => APP_URL . '/index.php?page=admin/users', 'icon' => 'group', 'label' => 'Manage Users'],
            ['url' => APP_URL . '/index.php?page=admin/events', 'icon' => 'event', 'label' => 'Manage Events', 'active' => true],
            ['url' => APP_URL . '/index.php?page=admin/bookings', 'icon' => 'confirmation_number', 'label' => 'All Bookings'],
            ['url' => APP_URL . '/index.php?page=admin/revenue', 'icon' => 'bar_chart', 'label' => 'Revenue Report'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_admin.php';
        require_once __DIR__ . '/../views/admin/events.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function adminUsers(): void {
        requireRole('admin');
        $pageTitle = 'Manage Users';
        $hideNav = true;
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $search = trim($_GET['search'] ?? '');
        $role = $_GET['role'] ?? '';
        $page = max(1, (int)($_GET['p'] ?? 1));
        $total = $userModel->countAll($search, $role);
        $pg = paginate($total, 20, $page);
        $users = $userModel->getAll($search, $role, $pg['per_page'], $pg['offset']);

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=admin/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['url' => APP_URL . '/index.php?page=admin/users', 'icon' => 'group', 'label' => 'Manage Users', 'active' => true],
            ['url' => APP_URL . '/index.php?page=admin/events', 'icon' => 'event', 'label' => 'Manage Events'],
            ['url' => APP_URL . '/index.php?page=admin/bookings', 'icon' => 'confirmation_number', 'label' => 'All Bookings'],
            ['url' => APP_URL . '/index.php?page=admin/revenue', 'icon' => 'bar_chart', 'label' => 'Revenue Report'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_admin.php';
        require_once __DIR__ . '/../views/admin/users.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function organizerDashboard(): void {
        requireRole('organizer');
        $pageTitle = 'Organizer Dashboard';
        $hideNav = true;
        $bookingModel = new Booking();
        $eventModel = new Event();
        $stats = $bookingModel->organizerStats(currentUserId());
        $events = $eventModel->getByOrganizer(currentUserId());

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=organizer/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'active' => true],
            ['url' => APP_URL . '/index.php?page=organizer/create', 'icon' => 'add_circle', 'label' => 'Create Event'],
            ['url' => APP_URL . '/index.php?page=organizer/events', 'icon' => 'event', 'label' => 'My Events'],
            ['url' => APP_URL . '/index.php', 'icon' => 'explore', 'label' => 'Browse Events'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_organizer.php';
        require_once __DIR__ . '/../views/organizer/dashboard.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function organizerHome(): void {
        requireRole('organizer');
        redirect(APP_URL . '/index.php?page=organizer/dashboard');
    }
}