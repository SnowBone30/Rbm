<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $rawPassword = $_POST['password'];
    $role = $_POST['role'];

    $canCreate = isset($_POST['can_create']) ? 1 : 0;
    $canRead   = isset($_POST['can_read'])   ? 1 : 0;
    $canEdit   = isset($_POST['can_edit'])   ? 1 : 0;
    $canDelete = isset($_POST['can_delete']) ? 1 : 0;

    // Hash the password
    $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

    if (!empty($username) && !empty($rawPassword) && !empty($role)) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, can_create, can_read, can_edit, can_delete) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiii", $username, $hashedPassword, $role, $canCreate, $canRead, $canEdit, $canDelete);
        $stmt->execute();
         header("Location: create_user.php?success=1");
    exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7f9fc;
            padding: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #0d6efd;
        }

        form {
            width: 500px;
            margin: auto;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 90%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

         
        select {
            width: 94%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .permissions {
            margin-top: 15px;
        }

         .permissions label {
            display: inline-block;
            margin-right: 15px;
        }

        .btn-container {
            text-align: center;
            margin-top: 25px;
        }

        button {
            background-color: #0d6efd;
            color: white;
            padding: 10px 25px;
            font-size: 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #0a58ca;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php if (isset($_GET['success'])): ?>
    <script>
        alert("User has been successfully created!");
         document.querySelector('form').reset();
    </script>
<?php endif; ?>


<h2><i class="fas fa-user-plus"></i> Create New User</h2>

<form method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <label for="role">Role:</label>
    <select name="role" id="role" required>
        <option value="staff">Staff</option>
        <option value="admin">Admin</option>
    </select>

    <div class="permissions">
        <label><input type="checkbox" name="can_create"> Can Create</label>
        <label><input type="checkbox" name="can_read"> Can Read</label>
        <label><input type="checkbox" name="can_edit"> Can Edit</label>
        <label><input type="checkbox" name="can_delete"> Can Delete</label>
    </div>

    <div class="btn-container">
        <button type="submit"><i class="fas fa-save"></i> Save User</button>
    </div>
</form>

<div class="back-link">
    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to User List</a>
</div>

</body>
</html>
