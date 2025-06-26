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
    <link rel="stylesheet" href="ul.css">

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
   
$netIp = $row['ip_address'];                   // fallback
if (filter_var($row['ip_address'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $netIp = $row['ip_address'] . '/24';
} elseif (filter_var($row['ip_address'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    $netIp = $row['ip_address'] . '/64';      
}
    ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['role']) ?></td>
        <td class="status-<?= $row['status'] === 'success' ? 'success' : 'failed' ?>">
            <?= ucfirst($row['status']) ?>
        </td>
       <td><?= $netIp ?></td>
        <td><?= $row['created_at'] ?></td>
        <td class="account-<?= $row['account_status'] === 'inactive' ? 'inactive' : 'active' ?>">
            <?= ucfirst($row['account_status'] ?? 'Unknown') ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
