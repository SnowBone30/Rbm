<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid role ID.</p>";
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT username, role, can_create, can_read, can_edit, can_delete FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$role = $result->fetch_assoc();

if (!$role) {
    echo "<p>User not found.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newRole = trim($_POST['role']);
    $newPass = trim($_POST['password']);
    $canCreate = isset($_POST['Create']) ? 1 : 0;
    $canRead = isset($_POST['Read']) ? 1 : 0;
    $canEdit = isset($_POST['Edit']) ? 1 : 0;
    $canDelete = isset($_POST['Delete']) ? 1 : 0;

    // If password field is not empty, update it
    if (!empty($newPass)) {
        $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, role = ?, can_create = ?, can_read = ?, can_edit = ?, can_delete = ? WHERE id = ?");
        $stmt->bind_param("ssiiiii", $hashedPassword, $newRole, $canCreate, $canRead, $canEdit, $canDelete, $id);
    } else {
        // Password not changed
        $stmt = $conn->prepare("UPDATE users SET role = ?, can_create = ?, can_read = ?, can_edit = ?, can_delete = ? WHERE id = ?");
        $stmt->bind_param("siiiii", $newRole, $canCreate, $canRead, $canEdit, $canDelete, $id);
    }

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "<p>Error updating user.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="ed.css">
</head>
<body>

<div class="form-container">
    <h2><i class="fas fa-pen-to-square"></i> Edit User</h2>

    <form method="POST">
        <label for="role">Role</label>
        <select name="role" id="role" required>
            <option value="staff" <?= $role['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
            <option value="admin" <?= $role['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <label for="password">New Password (leave blank to keep current)</label>
        <input type="password" name="password" id="password">

        <div class="permissions">
            <label><input type="checkbox" name="Create" <?= $role['can_create'] ? 'checked' : '' ?>> Create</label>
            <label><input type="checkbox" name="Read" <?= $role['can_read'] ? 'checked' : '' ?>> Read</label>
            <label><input type="checkbox" name="Edit" <?= $role['can_edit'] ? 'checked' : '' ?>> Edit</label>
            <label><input type="checkbox" name="Delete" <?= $role['can_delete'] ? 'checked' : '' ?>> Delete</label>
        </div>

        <button type="submit"><i class="fas fa-save"></i> Update User</button>
    </form>

    <div class="back-link">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

</body>
</html>