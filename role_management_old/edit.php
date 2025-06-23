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
$stmt = $conn->prepare("SELECT username, password, role, can_create, can_read, can_edit, can_delete FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$role = $result->fetch_assoc();

if (!$role) {
    echo "<p>Role not found.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newName = trim($_POST['role']);
    $newPass = trim($_POST['password']);
    $canCreate = isset($_POST['Create']) ? 1 : 0;
    $canRead = isset($_POST['Read']) ? 1 : 0;
    $canEdit = isset($_POST['Edit']) ? 1 : 0;
    $canDelete = isset($_POST['Delete']) ? 1 : 0;

     $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);

      if (!empty($username) && !empty($newPass) && !empty($role)) {
         $stmt = $conn->prepare("UPDATE users SET password = ?, role = ?, can_create = ?, can_read = ?, can_edit = ?, can_delete = ? WHERE id = ?");
    $stmt->bind_param("ssiiiii", $hashedPassword, $newName, $canCreate, $canRead, $canEdit, $canDelete, $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
    }

   
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Role</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
         body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            padding: 30px;
        }

        .form-container {
            background-color: #fff;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 12px;
            font-weight: 500;
        }

        input[type="text"], [type="password"] {
            width: 95%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .permissions {
            margin-bottom: 20px;
        }

        .permissions label {
            display: inline-block;
            margin-right: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0a58ca;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            text-decoration: none;
            color: #198754;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }


    </style>
</head>
<body>

<div class="form-container">
    <h2><i class="fas fa-pen-to-square"></i> Edit User</h2>

    <form method="POST">
        <label for="role"><i class="fas fa-id-badge"></i> Role Name</label>
        <select name="role" id="role" required>
        <option value="staff">staff</option>
        <option value="admin">Admin</option>
    </select>

     <form method="POST">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
    </select>

    

        <div class="permissions">
            <label><input type="checkbox" name="Create" value="1" <?= $role['can_create'] ? 'checked' : '' ?>> <i class="fas fa-plus-circle"></i> Create</label>
            <label><input type="checkbox" name="Read" value="1" <?= $role['can_read'] ? 'checked' : '' ?>> <i class="fas fa-eye"></i> Read</label>
            <label><input type="checkbox" name="Edit" value="1" <?= $role['can_edit'] ? 'checked' : '' ?>> <i class="fas fa-edit"></i> Edit</label>
            <label><input type="checkbox" name="Delete" value="1" <?= $role['can_delete'] ? 'checked' : '' ?>> <i class="fas fa-trash-alt"></i> Delete</label>
        </div>

        <button type="submit"><i class="fas fa-save"></i> Update Role</button>
    </form>

    <div class="back-link">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Role List</a>
    </div>
</div>

</body>
</html>
