<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../db/connection.php';

// UPDATE ORDER STATUS
$success = '';
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status   = $_POST['status'];
    $crud->updateOrderStatus($order_id, $status);
    $success = 'Order status updated!';
}

$orders = $crud->getAllOrders();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
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
        .badge-pending  { background: #ffc107; color: black; padding: 5px 10px; border-radius: 5px; }
        .badge-delivered { background: #28a745; color: white; padding: 5px 10px; border-radius: 5px; }
        .badge-cancelled { background: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; }
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
            <a href="orders.php" class="active">🛒 Orders</a>
            <a href="users.php">👥 Users</a>
            <a href="../index.php">🏠 View Store</a>
            <a href="logout.php">🚪 Logout</a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-10 p-4">

            <h2 class="mb-4">🛒 Manage Orders</h2>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($orders) == 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No orders yet</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach($orders as $o): ?>
                            <tr>
                                <td><?php echo $o['id']; ?></td>
                                <td><?php echo $o['full_name']; ?></td>
                                <td><?php echo $o['email']; ?></td>
                                <td>$<?php echo $o['total_amount']; ?></td>
                                <td>
                                    <span class="badge-<?php echo $o['status']; ?>">
                                        <?php echo ucfirst($o['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $o['created_at']; ?></td>
                                <td>
                                    <form method="POST" class="d-flex">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <select name="status" class="form-control form-control-sm mr-2">
                                            <option value="pending"   <?php echo $o['status']=='pending'   ? 'selected':'' ?>>Pending</option>
                                            <option value="delivered" <?php echo $o['status']=='delivered' ? 'selected':'' ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $o['status']=='cancelled' ? 'selected':'' ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary btn-sm">
                                            Update
                                        </button>
                                    </form>
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