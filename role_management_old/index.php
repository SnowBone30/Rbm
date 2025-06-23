<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'staff') {
    $userId = $_SESSION['user']['id'];

    // Fetch current user info from DB
    $stmt = $conn->prepare("SELECT account_status, deactivation_requested_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['deactivation_requested_at']) {
        $requested = new DateTime($user['deactivation_requested_at']);
        $now = new DateTime();
        $interval = $requested->diff($now);

        if ($interval->days >= 3 && $user['account_status'] === 'active') {
            // Auto deactivate
            $deactivate = $conn->prepare("UPDATE users SET account_status = 'inactive' WHERE id = ?");
            $deactivate->bind_param("i", $userId);
            $deactivate->execute();

            session_destroy();
            header("Location: login.php?error=deactivated");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Role Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7f9fc;
            padding: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .top-bar p {
            font-size: 16px;
            margin: 0;
        }

        .top-bar a {
            text-decoration: none;
            color: #e63946;
            font-weight: bold;
        }

        .create-button {
            margin-bottom: 20px;
        }

        .create-button a {
            background-color: #0d6efd;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .create-button a:hover {
            background-color: #0a58ca;
        }

        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #0d6efd;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-icons a {
            margin: 0 5px;
            text-decoration: none;
            color: #0d6efd;
        }

        .action-icons a:hover {
            color: #0a58ca;
        }

        .header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            margin: auto;
            margin-bottom: 20px;
        }

        .footer-button {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            margin: auto;
            margin-bottom: 20px;
        }

        .header-controls .create-button {
            margin: 0;
        }

        .header-controls .create-button a {
            padding: 8px 16px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <?php if (isset($_GET['pending'])): ?>
        <p style="color: orange; font-weight: bold; text-align: center;">
            Deactivation requested. Your account will be deactivated after 3 days.
        </p>
    <?php endif; ?>

    <h2><i class="fa-solid fa-user-gear"></i> Manage Roles</h2>

    <div class="header-controls">
        <div class="top-bar">
            <p>
                Welcome, <strong><?= $_SESSION['user']['username'] ?></strong>
                (<?= $_SESSION['user']['role'] ?>)
            </p>
        </div>

        <?php if ($_SESSION['user']['role'] === 'staff'): ?>
            <form method="POST" action="deactivate_account.php"
                onsubmit="return confirm('Are you sure you want to deactivate your account? This will take effect after 3 days.');">
                <button type="submit"
                    style="background-color: red; color: white; padding: 10px 20px; border-radius: 6px; border: none;">
                    Deactivate My Account
                </button>
            </form>
        <?php endif; ?>


        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <div class="create-button">
                <a href="create_user.php"><i class="fas fa-plus-circle"></i> Create New User</a>
                <a href="user_logs.php"> View user logs</a>
            </div>
        <?php endif; ?>
    </div>



    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Role</th>
            <th>Actions</th>
             <th>Deactivate Account</th>
        </tr>
        <?php
        $result = $conn->query("SELECT id, username, role FROM users ORDER BY id DESC");
        while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td class="action-icons">
                    <a href="view.php?id=<?= $row['id'] ?>" title="View"><i class="fas fa-eye"></i></a>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="edit.php?id=<?= $row['id'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                        <a href="delete.php?id=<?= $row['id'] ?>" title="Delete" onclick="return confirm('Delete this role?')">
                            <i class="fas fa-trash"></i>
                        </a>
                        <td>
                            <form method="POST" action="deactivate_account.php"
                onsubmit="return confirm('Are you sure you want to deactivate your account? This will take effect after 3 days.');">
                <button type="submit"
                    style="background-color: red; color: white; padding: 10px 20px; border-radius: 6px; border: none;">
                    Deactivate My Account
                </button>
                        </td>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <br>

    <div class="footer-button">
        <div class="create-button">
            <a href="logout.php"> Logout</a>
        </div>
    </div>

</body>

</html>