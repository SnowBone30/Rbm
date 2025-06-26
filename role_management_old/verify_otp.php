<?php
session_start();
include 'db.php';                       

/* abort OTP flow if the account is now inactive ------------------ */
$userId = $_SESSION['otp_user']['id'] ?? 0;
$stmt   = $conn->prepare("SELECT account_status FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$status = $stmt->get_result()->fetch_assoc()['account_status'] ?? 'inactive';

if ($status === 'inactive') {
    /* clean up one-time data */
    unset($_SESSION['otp'], $_SESSION['otp_user'], $_SESSION['otp_message']);

    /* optional banner for login.php */
    $_SESSION['error'] = 'Your account has been deactivated. Contact the administrator.';

    header('Location: login.php');
    exit;
}


// Redirect if someone lands here without starting the OTP flow
if (!isset($_SESSION['otp'], $_SESSION['otp_user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp  = trim($_POST['otp'] ?? '');      // always a string
    $expectedOtp = (string) $_SESSION['otp'];      // cast for strict compare

    if ($enteredOtp === $expectedOtp) {            // ✅ OTP matches
        // Promote the visitor to a fully logged‑in session
        $_SESSION['user'] = $_SESSION['otp_user']; // index.php expects this key

        // Clean up one‑time data
        unset($_SESSION['otp'], $_SESSION['otp_user'], $_SESSION['otp_message']);
        session_regenerate_id(true);               // defeat session fixation

        header('Location: index.php');             // send to home/dashboard
        exit;
    }

    // ❌ Wrong code – bounce back with an error message
    $_SESSION['error'] = 'Invalid OTP. Please try again.';
    header('Location: verify_otp.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .wrapper {
            width: 320px;
            margin: 120px auto;
            background: #f7f9fc;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            background-color: #0d6efd;
            color: #fff;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            margin-top: 20px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0a58ca;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .demo {
            background: #eef;
            padding: 8px;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>OTP Verification</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['otp_message'])): ?>
            <div class="demo">
                <?php echo htmlspecialchars($_SESSION['otp_message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="otp">Enter 6‑digit code:</label>
            <input type="text" id="otp" name="otp" pattern="\d{6}" maxlength="6" required autofocus>
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>
