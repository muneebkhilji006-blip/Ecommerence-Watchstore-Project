<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../db/connection.php';

// Count stats
$totalUsers    = $crud->countUsers();
$totalProducts = $crud->countProducts();
$totalOrders   = $crud->countOrders();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
        .stat-card {
            border-radius: 15px;
            padding: 25px;
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }
        .stat-card h2 { font-size: 48px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar">
            <h4>⌚ Admin</h4>
            <a href="dashboard.php" class="active">📊 Dashboard</a>
            <a href="products.php">📦 Products</a>
            <a href="orders.php">🛒 Orders</a>
            <a href="users.php">👥 Users</a>
            <a href="../index.php">🏠 View Store</a>
            <a href="logout.php">🚪 Logout</a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-10 p-4">

            <h2 class="mb-4">Welcome, <?php echo $_SESSION['admin_name']; ?>! 👋</h2>

            <div class="row">

                <div class="col-md-4">
                    <div class="stat-card bg-primary">
                        <h2><?php echo $totalUsers; ?></h2>
                        <h5>Total Users</h5>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card bg-success">
                        <h2><?php echo $totalProducts; ?></h2>
                        <h5>Total Products</h5>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card bg-warning">
                        <h2><?php echo $totalOrders; ?></h2>
                        <h5>Total Orders</h5>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>