<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 30px;
            background-color: #f7f9fc;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #0d6efd;
        }

        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
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

        .status-success {
            color: green;
            font-weight: bold;
        }

        .status-failed {
            color: red;
            font-weight: bold;
        }

        .account-active {
            color: green;
            font-weight: bold;
        }

        .account-inactive {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2><i class="fas fa-history"></i> User Login History</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Status</th>
        <th>IP Address</th>
        <th>Timestamp</th>
        <th>Account</th>
    </tr>
    <?php
    // Join login_logs with users to get account status
    $query = "
        SELECT l.*, u.account_status AS account_status 
        FROM login_logs l 
        LEFT JOIN users u ON l.username = u.username 
        ORDER BY l.created_at DESC
    ";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()):
    ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['role']) ?></td>
        <td class="status-<?= $row['status'] === 'success' ? 'success' : 'failed' ?>">
            <?= ucfirst($row['status']) ?>
        </td>
        <td><?= $row['ip_address'] ?></td>
        <td><?= $row['created_at'] ?></td>
        <td class="account-<?= $row['account_status'] === 'inactive' ? 'inactive' : 'active' ?>">
            <?= ucfirst($row['account_status'] ?? 'Unknown') ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
