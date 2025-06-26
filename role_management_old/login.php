<?php
session_start();
include 'db.php';

function getClientIp(): string
{
    $headers = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_REAL_IP',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($headers as $key) {
        if (!empty($_SERVER[$key])) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);

                /* special-case the IPv6 loop-back first */
                if ($ip === '::1') {
                    return '127.0.0.1';
                }

                /* validate – allow private/LAN, skip only invalid format        */
                if (
                    filter_var(
                        $ip,
                        FILTER_VALIDATE_IP,
                        FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6
                    )
                ) {
                    return $ip;
                }
            }
        }
    }
    return '0.0.0.0';   // nothing usable found
}


function logLoginAttempt($conn, $username, $role, $status)
{
    $ip = getClientIp();                // ← use helper
    $stmt = $conn->prepare(
        "INSERT INTO login_logs (username, role, status, ip_address)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $username, $role, $status, $ip);
    $stmt->execute();
}


function deactivateInactiveUsers($conn)
{
    $stmt = $conn->prepare("UPDATE users SET account_status = 'inactive' WHERE last_login < NOW() - INTERVAL 3 DAY AND account_status = 'active'");
    $stmt->execute();
}

deactivateInactiveUsers($conn);

// Function to log login attempts


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();


    if ($user) {
        /* right after $user = $result->fetch_assoc(); */
        if ($user && $user['account_status'] === 'inactive') {
            $_SESSION['error'] = 'Your account has been deactivated. Contact the administrator.';
            logLoginAttempt($conn, $username, $user['role'], 'failed');
            header('Location: login.php');
            exit;
        } else {
            // Check lockout
            $now = new DateTime();
            $lastFailed = new DateTime($user['last_failed_login'] ?? '2000-01-01');
            $interval = $lastFailed->diff($now);

            if ($user['failed_attempts'] >= 5 && $interval->days < 1) {
                $error = "Too many failed attempts. Try again after 24 hours.";
                logLoginAttempt($conn, $username, $user['role'], 'failed');

            } else {
                // Check password
                if (password_verify($password, $user['password'])) {
                    // Reset failed attempts
                    /* 1. reset failed attempts */
                    $reset = $conn->prepare(
                        "UPDATE users
     SET failed_attempts = 0,
         last_failed_login = NULL
     WHERE id = ?"
                    );
                    $reset->bind_param("i", $user['id']);
                    $reset->execute();

                    /* 2. generate & stash OTP */
                    $otp = rand(100000, 999999);
                    $_SESSION['otp'] = $otp;
                    $_SESSION['otp_user'] = $user;

                    /* 3. (demo only) store a message you can display on verify_otp.php */
                    $_SESSION['otp_message'] = "Your OTP is: $otp";

                    logLoginAttempt($conn, $user['username'], $user['role'], 'success');

                    /* 5. redirect to OTP page */
                    header("Location: verify_otp.php");
                    exit;

                } else {
                    // Increment failed attempts
                    $update = $conn->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE id = ?");
                    $update->bind_param("i", $user['id']);
                    $update->execute();

                    logLoginAttempt($conn, $user['username'], $user['role'], 'failed');
                    $remaining = 5 - ($user['failed_attempts'] + 1);
                    $error = $remaining > 0
                        ? "Invalid credentials. You have $remaining attempt(s) left."
                        : "Too many failed attempts. Try again after 24 hours.";
                }
            }
        }

    } else {
        // Username doesn't exist
        $error = "Invalid username or password.";
        logLoginAttempt($conn, $username, 'unknown', 'failed');
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        form {
            width: 320px;
            margin: 100px auto;
            background: #f7f9fc;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            background-color: #0d6efd;
            color: white;
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
    </style>
</head>

<body>

    <form method="POST">
        <h2>Login</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']);
        }
        ?>


        <?php if (isset($error))
            echo "<div class='error'>$error</div>"; ?>

        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>

</body>

</html>