<?php
// controllers/BookingController.php
declare(strict_types=1);

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../core/csrf_helper.php';
require_once __DIR__ . '/../core/session_helper.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Ticket.php';

requireLogin();

$action = $_POST['action'] ?? '';

match($action) {
    'book'   => handleBook(),
    'cancel' => handleCancelBooking(),
    default  => redirect('events'),
};


function handleBook(): void
{
    requireRole('attendee', 'admin');
    validateCsrfToken($_POST['csrf_token'] ?? '');

    $eventId  = (int)($_POST['event_id']  ?? 0);
    $ticketId = (int)($_POST['ticket_id'] ?? 0);
    $quantity = max(1, min(10, (int)($_POST['quantity'] ?? 1)));

    if (!$eventId || !$ticketId) {
        setFlash('error', 'Invalid booking request.');
        redirect('events');
    }

    $ticketModel = new Ticket();
    $tickets     = $ticketModel->getByEvent($eventId);
    $ticket      = null;
    foreach ($tickets as $t) {
        if ((int)$t['id'] === $ticketId) { $ticket = $t; break; }
    }

    if (!$ticket) {
        setFlash('error', 'Ticket type not found.');
        redirect('events');
    }

    if ($ticket['available'] < $quantity) {
        setFlash('error', 'Not enough seats available.');
        redirect('event.detail');
    }

    $pdo = getDB();
    $pdo->beginTransaction();
    try {
        $bookingModel = new Booking();
        $bookingModel->create([
            'attendee_id'  => currentUserId(),
            'event_id'     => $eventId,
            'ticket_id'    => $ticketId,
            'quantity'     => $quantity,
            'total_amount' => (float)$ticket['price'] * $quantity,
        ]);
        $ticketModel->decrementStock($ticketId, $quantity);
        $pdo->commit();
        setFlash('success', 'Booking confirmed! Check My Bookings for your ticket details.');
        redirect('attendee.bookings');
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('[EMS BOOKING ERROR] ' . $e->getMessage());
        setFlash('error', 'Booking failed. Please try again.');
        redirect('events');
    }
}

function handleCancelBooking(): void
{
    validateCsrfToken($_POST['csrf_token'] ?? '');
    $bookingId = (int)($_POST['booking_id'] ?? 0);
    if (!$bookingId) { redirect('attendee.bookings'); }

    $bookingModel = new Booking();
    $bookingModel->cancel($bookingId, currentUserId());

    setFlash('success', 'Booking cancelled successfully.');
    redirect('attendee.bookings');
}

function redirect(string $page): never
{
    header('Location: ' . BASE_PATH . "/index.php?page=$page");
    exit;
}
