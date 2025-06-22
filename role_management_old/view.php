<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid role ID.</p>";
    exit;
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT id, role, can_create, can_read, can_edit, can_delete FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$role = $result->fetch_assoc();

if (!$role) {
    echo "<p>Role not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Role</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="view.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>

<div class="container">
    <h2>🔎 View Role</h2>

    <p><strong>ID:</strong> <?= $role['id'] ?></p>
    <p><strong>Role Name:</strong> <?= htmlspecialchars($role['role']) ?></p>

    <p><strong>Permissions:</strong></p>
    <ul>
        <li><i class="fas fa-plus-circle"></i> Create: <?= $role['can_create'] ? '✅' : '❌' ?></li>
        <li><i class="fas fa-eye"></i> Read: <?= $role['can_read'] ? '✅' : '❌' ?></li>
        <li><i class="fas fa-pen"></i> Edit: <?= $role['can_edit'] ? '✅' : '❌' ?></li>
        <li><i class="fas fa-trash"></i> Delete: <?= $role['can_delete'] ? '✅' : '❌' ?></li>
    </ul>

    <div class="back-link">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Role List</a>
    </div>
</div>

</body>
</html>
