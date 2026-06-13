<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../db/connection.php';

// DELETE USER
$success = '';
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $crud->deleteUser($id);
    $success = 'User deleted successfully!';
}

$users = $crud->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f4f4f4; }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            padding: 20px;
        }
        .sidebar h4 { color: white; margin-bottom: 30px; }
        .sidebar a {
            display: block;
            color: #adb5bd;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            text-decoration: none;
            font-weight: bold;
        }
        .sidebar a:hover { background: #495057; color: white; }
        .sidebar a.active { background: #007bff; color: white; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar">
            <h4>⌚ Admin</h4>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="products.php">📦 Products</a>
            <a href="orders.php">🛒 Orders</a>
            <a href="users.php" class="active">👥 Users</a>
            <a href="../index.php">🏠 View Store</a>
            <a href="logout.php">🚪 Logout</a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-10 p-4">

            <h2 class="mb-4">👥 Manage Users</h2>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Registered On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($users) == 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No users found</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach($users as $u): ?>
                            <tr>
                                <td><?php echo $u['id']; ?></td>
                                <td><?php echo $u['full_name']; ?></td>
                                <td><?php echo $u['email']; ?></td>
                                <td><?php echo $u['created_at']; ?></td>
                                <td>
                                    <a href="users.php?delete=<?php echo $u['id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this user?')">
                                        🗑️ Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>