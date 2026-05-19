<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/Event.php';


require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../core/csrf_helper.php';
require_once __DIR__ . '/../core/session_helper.php';
require_once __DIR__ . '/../models/Booking.php';

class BookingController {

    public function checkout(): void {
        requireRole('attendee');
        $pageTitle = 'Checkout';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCSRF()) {
            redirect(APP_URL . '/index.php?page=events');
        }

        $eventModel = new Event();
        $bookingModel = new Booking();
        $eventId = (int)($_POST['event_id'] ?? 0);
        $qty = max(1, min(5, (int)($_POST['quantity'] ?? 1)));
        $event = $eventModel->findById($eventId);

        if (!$event || $event['status'] !== 'published' || $event['available_seats'] < $qty) {
            setFlash('error', 'Unable to proceed with booking.');
            redirect(APP_URL . '/index.php?page=events');
        }

        $total = $event['ticket_price'] * $qty;

        if (isset($_POST['confirm_booking'])) {
            $bookingRef = generateBookingRef();
            $bookingId = $bookingModel->create($bookingRef, currentUserId(), $eventId, $qty, $total);
            $bookingModel->confirm($bookingId);
            $eventModel->decrementSeats($eventId, $qty);
            $bookingModel->createPayment($bookingId, 'DIRECT-' . $bookingRef, $total);

            setFlash('success', 'Booking confirmed successfully.');
            redirect(APP_URL . '/index.php?page=confirmation&ref=' . urlencode($bookingRef));
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/attendee/checkout.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function confirmation(): void {
        $pageTitle = 'Booking Confirmed';
        $hideNav = false;
        $ref = $_GET['ref'] ?? '';
        $bookingModel = new Booking();
        $booking = $bookingModel->findByRef($ref);
        if (!$booking) { redirect(APP_URL . '/index.php?page=events'); }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/attendee/confirmation.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // ── eSewa Payment ────────────────────────────────────────────────────────

    public function esewaInitiate(): void {
        requireRole('attendee');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCSRF()) {
            redirect(APP_URL . '/index.php?page=events');
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $qty     = max(1, min(5, (int)($_POST['quantity'] ?? 1)));

        $eventModel   = new Event();
        $bookingModel = new Booking();
        $event        = $eventModel->findById($eventId);

        if (!$event || $event['status'] !== 'published' || $event['available_seats'] < $qty) {
            setFlash('error', 'Unable to proceed with booking.');
            redirect(APP_URL . '/index.php?page=events');
        }

        $totalAmount  = number_format($event['ticket_price'] * $qty, 2, '.', '');
        $bookingRef   = generateBookingRef();

        // Create a PENDING booking (seats reserved, confirmed after payment)
        $bookingModel->create($bookingRef, currentUserId(), $eventId, $qty, (float)$totalAmount);

        require_once __DIR__ . '/../core/EsewaHelper.php';

        $esewaData = [
            'amount'                  => $totalAmount,
            'tax_amount'              => '0',
            'total_amount'            => $totalAmount,
            'transaction_uuid'        => $bookingRef,
            'product_code'            => EsewaHelper::MERCHANT_CODE,
            'product_service_charge'  => '0',
            'product_delivery_charge' => '0',
            'success_url'             => APP_URL . '/index.php?page=esewa/success',
            'failure_url'             => APP_URL . '/index.php?page=esewa/failure',
            'signed_field_names'      => 'total_amount,transaction_uuid,product_code',
            'signature'               => EsewaHelper::signature($totalAmount, $bookingRef, EsewaHelper::MERCHANT_CODE),
        ];

        $pageTitle = 'Redirecting to eSewa';
        $hideNav   = true;
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/esewa/redirect.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function esewaSuccess(): void {
        // Note: no requireLogin() here — the signed eSewa payload itself
        // authenticates the transaction. The browser may or may not still
        // have our session cookie after the cross-domain redirect, so we
        // resolve the user from the pending booking instead.

        $encodedData = $_GET['data'] ?? '';
        if (!$encodedData) {
            error_log('eSewa success: no data param received');
            setFlash('error', 'Payment verification failed: no data received.');
            redirect(APP_URL . '/index.php?page=events');
        }

        $data = json_decode(base64_decode($encodedData), true);
        if (!is_array($data)) {
            error_log('eSewa success: invalid base64/JSON payload');
            setFlash('error', 'Payment verification failed: invalid response.');
            redirect(APP_URL . '/index.php?page=events');
        }

        require_once __DIR__ . '/../core/EsewaHelper.php';

        if (!EsewaHelper::verifyResponse($data)) {
            error_log('eSewa success: signature mismatch for uuid=' . ($data['transaction_uuid'] ?? '?'));
            setFlash('error', 'Payment signature mismatch. Please contact support.');
            redirect(APP_URL . '/index.php?page=events');
        }

        $txnUuid     = $data['transaction_uuid'];
        $totalAmount = $data['total_amount'];
        $txnCode     = $data['transaction_code'] ?? ('ESW-' . $txnUuid);

        if (!EsewaHelper::checkStatus($txnUuid, $totalAmount, EsewaHelper::MERCHANT_CODE)) {
            error_log("eSewa success: status API did not return COMPLETE for {$txnUuid}");
            setFlash('error', 'Payment not verified by eSewa. Please contact support.');
            redirect(APP_URL . '/index.php?page=events');
        }

        $bookingModel = new Booking();
        $booking      = $bookingModel->findByRef($txnUuid);

        if (!$booking) {
            error_log("eSewa success: booking not found for uuid={$txnUuid}");
            setFlash('error', 'Booking not found. Please contact support.');
            redirect(APP_URL . '/index.php?page=events');
        }

        if ($booking['status'] === 'pending') {
            $eventModel = new Event();
            $bookingModel->confirm($booking['booking_id']);
            $eventModel->decrementSeats($booking['event_id'], $booking['quantity']);
            $bookingModel->createPayment($booking['booking_id'], $txnCode, (float)$totalAmount, 'esewa');
        }

        setFlash('success', 'Payment successful! Your booking is confirmed.');
        redirect(APP_URL . '/index.php?page=confirmation&ref=' . urlencode($txnUuid));
    }

    public function esewaFailure(): void {
        // Cancel the pending booking so seats are freed
        $encodedData = $_GET['data'] ?? '';
        if ($encodedData) {
            $data = json_decode(base64_decode($encodedData), true);
            if (is_array($data) && !empty($data['transaction_uuid'])) {
                $bookingModel = new Booking();
                $booking      = $bookingModel->findByRef($data['transaction_uuid']);
                if ($booking && $booking['status'] === 'pending') {
                    $bookingModel->cancelPending($booking['booking_id']);
                }
            }
        }

        setFlash('error', 'eSewa payment was cancelled or failed. Please try again.');
        redirect(APP_URL . '/index.php?page=events');
    }

    public function cancel(): void {
        requireRole('attendee');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF()) {
            $id = (int)($_POST['booking_id'] ?? 0);
            $bookingModel = new Booking();
            $bookingModel->cancel($id, currentUserId());
            setFlash('success', 'Booking cancelled.');
        }
        redirect(APP_URL . '/index.php?page=attendee/bookings');
    }

    public function attendeeDashboard(): void {
        requireRole('attendee');
        $pageTitle = 'My Dashboard';
        $hideNav = true;
        $bookingModel = new Booking();
        $stats = $bookingModel->attendeeStats(currentUserId());
        $upcoming = $bookingModel->getByAttendee(currentUserId(), 'upcoming');

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=attendee/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'active' => true],
            ['url' => APP_URL . '/index.php?page=attendee/bookings', 'icon' => 'confirmation_number', 'label' => 'My Bookings'],
            ['url' => APP_URL . '/index.php', 'icon' => 'explore', 'label' => 'Browse Events'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_attendee.php';
        require_once __DIR__ . '/../views/attendee/dashboard.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function attendeeBookings(): void {
        requireRole('attendee');
        $pageTitle = 'My Bookings';
        $hideNav = true;
        $bookingModel = new Booking();
        $type = $_GET['type'] ?? 'upcoming';
        $bookings = $bookingModel->getByAttendee(currentUserId(), $type);

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=attendee/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['url' => APP_URL . '/index.php?page=attendee/bookings', 'icon' => 'confirmation_number', 'label' => 'My Bookings', 'active' => true],
            ['url' => APP_URL . '/index.php', 'icon' => 'explore', 'label' => 'Browse Events'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_attendee.php';
        require_once __DIR__ . '/../views/attendee/bookings.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function rate(): void {
        requireRole('attendee');
        $pageTitle = 'Rate Event';
        $hideNav = true;
        $eventId = (int)($_GET['event_id'] ?? 0);
        $eventModel = new Event();
        $event = $eventModel->findById($eventId);
        if (!$event) { redirect(APP_URL . '/index.php?page=attendee/bookings'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF()) {
            $rating = max(1, min(5, (int)($_POST['rating'] ?? 0)));
            $text = trim($_POST['review_text'] ?? '');
            $bookingModel = new Booking();
            $bookingModel->addReview($eventId, currentUserId(), $rating, $text ?: null);
            setFlash('success', 'Review submitted!');
            redirect(APP_URL . '/index.php?page=attendee/bookings&type=past');
        }

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=attendee/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['url' => APP_URL . '/index.php?page=attendee/bookings', 'icon' => 'confirmation_number', 'label' => 'My Bookings', 'active' => true],
            ['url' => APP_URL . '/index.php', 'icon' => 'explore', 'label' => 'Browse Events'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_attendee.php';
        require_once __DIR__ . '/../views/attendee/rate.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

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
            ['url' => APP_URL . '/index.php?page=admin/commission', 'icon' => 'handshake', 'label' => 'Commissions'],
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
            ['url' => APP_URL . '/index.php?page=admin/commission', 'icon' => 'handshake', 'label' => 'Commissions'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_admin.php';
        require_once __DIR__ . '/../views/admin/revenue.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function attendeeProfile(): void {
        requireRole('attendee');
        $pageTitle = 'My Profile';
        $hideNav = true;
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $user = $userModel->findById(currentUserId());

        $sidebarLinks = [
            ['url' => APP_URL . '/index.php?page=attendee/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['url' => APP_URL . '/index.php?page=attendee/bookings', 'icon' => 'confirmation_number', 'label' => 'My Bookings'],
            ['url' => APP_URL . '/index.php?page=attendee/profile', 'icon' => 'person', 'label' => 'Profile', 'active' => true],
            ['url' => APP_URL . '/index.php', 'icon' => 'explore', 'label' => 'Browse Events'],
        ];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar_attendee.php';
        require_once __DIR__ . '/../views/attendee/profile.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

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
