<?php
/* -------------------------------------------------------------
 * reactivate_account.php
 * Admin-only endpoint: flip account_status from 'inactive' to 'active'.
 * Expects  POST username  from the Reactivate form.
 * -----------------------------------------------------------*/

session_start();

/* 0. Admin guard --------------------------------------------------- */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'db.php';

/* 1. Validate POST ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['username'])) {
    header('Location: user_logs.php?error=missing_username');
    exit;
}

$username = trim($_POST['username']);                 // value from hidden input

/* 2. Fetch target user -------------------------------------------- */
$userStmt = $conn->prepare(
    "SELECT account_status
       FROM users
      WHERE username = ?"
);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$target = $userStmt->get_result()->fetch_assoc();

if (!$target) {
    header('Location: user_logs.php?error=user_not_found');
    exit;
}

if ($target['account_status'] !== 'inactive') {
    header('Location: user_logs.php?error=already_active');
    exit;
}

/* 3. Reactivate (prepared UPDATE) --------------------------------- */
$update = $conn->prepare(
    "UPDATE users
        SET
         account_status            = 'active'
      WHERE username = ?"
);
$update->bind_param("s", $username);
$update->execute();

/* Optional: quick sanity debug — remove when happy
echo 'Affected rows: '.$update->affected_rows; exit;
*/

/* 4. Log admin action (optional) ---------------------------------- */
$log = $conn->prepare(
    "INSERT INTO login_logs (username, role, status, ip_address)
        VALUES (?, 'admin', 'reactivated', 'admin_action')"
);
$log->bind_param("s", $username);
$log->execute();

/* 5. Redirect back with success flag ----------------------------- */
header('Location: user_logs.php?reactivated=1');
exit;
?>
