<?php
session_start();
include 'db.php';

if (!isset($_SESSION['staff']) || $_SESSION['staff']['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['staff']['id'];

// Set the current timestamp as the deactivation request time
$stmt = $conn->prepare("UPDATE users SET deactivation_requested_at = NOW() WHERE id = ?");
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    // Redirect with a query flag to show the message on index.php
    header("Location: index.php?pending=1");
    exit;
} else {
    // Handle potential database error
    echo "Something went wrong while requesting deactivation. Please try again.";
}
?>
