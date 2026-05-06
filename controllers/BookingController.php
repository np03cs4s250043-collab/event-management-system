<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../core/csrf_helper.php';
require_once __DIR__ . '/../core/session_helper.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Ticket.php';

class BookingController {

    public function adminBookings(): void {
        requireRole('admin');
        $pageTitle = 'All Bookings';
        $hideNav = true;
        $bookingModel = new Booking();
        $page = max(1, (int)($_GET['p'] ?? 1));
        $total = $bookingModel->totalBookings();
        $pg = paginate($total, 20, $page);
        $bookings = $bookingModel->getAllBookings($pg['per_page'], $pg['offset']);

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=admin/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['url' => APP_URL . '/index.php?page=admin/users', 'icon' => 'group', 'label' => 'Manage Users'],
            ['url' => APP_URL . '/index.php?page=admin/events', 'icon' => 'event', 'label' => 'Manage Events'],
            ['url' => APP_URL . '/index.php?page=admin/bookings', 'icon' => 'confirmation_number', 'label' => 'All Bookings', 'active' => true],
            ['url' => APP_URL . '/index.php?page=admin/revenue', 'icon' => 'bar_chart', 'label' => 'Revenue Report'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_admin.php';
        require_once __DIR__ . '/../views/admin/bookings.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function adminRevenue(): void {
        requireRole('admin');
        $pageTitle = 'Revenue Report';
        $hideNav = true;
        $bookingModel = new Booking();
        $stats = $bookingModel->adminStats();
        $revenueByEvent = $bookingModel->revenueByEvent();

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=admin/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['url' => APP_URL . '/index.php?page=admin/users', 'icon' => 'group', 'label' => 'Manage Users'],
            ['url' => APP_URL . '/index.php?page=admin/events', 'icon' => 'event', 'label' => 'Manage Events'],
            ['url' => APP_URL . '/index.php?page=admin/bookings', 'icon' => 'confirmation_number', 'label' => 'All Bookings'],
            ['url' => APP_URL . '/index.php?page=admin/revenue', 'icon' => 'bar_chart', 'label' => 'Revenue Report', 'active' => true],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_admin.php';
        require_once __DIR__ . '/../views/admin/revenue.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
