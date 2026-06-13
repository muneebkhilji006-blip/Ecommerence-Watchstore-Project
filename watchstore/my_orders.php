<?php
require_once 'includes/header.php';
require_once 'db/connection.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user orders
$orders = $crud->getUserOrders($user_id);
?>

<style>
    body { padding-top: 80px; background: #f4f4f4; }
    footer { background: #343a40; color: white; padding: 20px 0; }
    .order-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .badge-pending   { background: #ffc107; color: black; padding: 5px 12px; border-radius: 20px; font-size: 13px; }
    .badge-delivered { background: #28a745; color: white; padding: 5px 12px; border-radius: 20px; font-size: 13px; }
    .badge-cancelled { background: #dc3545; color: white; padding: 5px 12px; border-radius: 20px; font-size: 13px; }
    .order-item-row {
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .order-item-row img {
        width: 55px;
        height: 55px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 12px;
    }
</style>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand font-weight-bold" href="index.php">⌚ WatchStore</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item mr-2">
    <span class="btn btn-outline-light">
        👤 <?php echo $_SESSION['user_name']; ?>
    </span>
</li>
<li class="nav-item mr-2">
    <span class="btn btn-outline-light">
        📞 <?php echo $_SESSION['user_phone']; ?>
    </span>
</li>
<li class="nav-item mr-2">
    <span class="btn btn-outline-light">
        📍 <?php echo $_SESSION['user_address']; ?>
    </span>
</li>
            <li class="nav-item mr-2">
                <a href="index.php" class="btn btn-outline-light">🏠 Home</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">

    <h2 class="mb-4">📦 My Orders</h2>

    <?php if(count($orders) == 0): ?>

        <div class="text-center mt-5">
            <h4 class="text-muted">You have no orders yet!</h4>
            <a href="index.php" class="btn btn-primary mt-3">Start Shopping</a>
        </div>

    <?php else: ?>

        <?php foreach($orders as $order): ?>

            <?php
            // Fetch order items
             $items = $crud->getOrderItems($order['id']);
            ?>

            <div class="card order-card">
                <div class="card-body">

                    <!-- ORDER HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
        <small class="text-muted"><?php echo $order['created_at']; ?></small>
        <br>
        <small class="text-muted">
            📞 <?php echo $_SESSION['user_phone']; ?> &nbsp;|&nbsp;
            📍 <?php echo $_SESSION['user_address']; ?>
        </small>
    </div>
    <div>
        <span class="badge-<?php echo $order['status']; ?>">
            <?php echo ucfirst($order['status']); ?>
        </span>
    </div>
</div>

                    <!-- ORDER ITEMS -->
                    <?php foreach($items as $item): ?>
                    <div class="order-item-row">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                        <div style="flex:1">
                            <h6 class="mb-0"><?php echo $item['name']; ?></h6>
                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                        </div>
                        <strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                    </div>
                    <?php endforeach; ?>

                    <!-- ORDER TOTAL -->
                    <div class="d-flex justify-content-between mt-3">
                        <h5>Total:</h5>
                        <h5 class="text-success">$<?php echo number_format($order['total_amount'], 2); ?></h5>
                    </div>

                </div>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<!-- FOOTER -->
<footer class="mt-5">
    <div class="container text-center">
        <p>⌚ WatchStore &copy; 2026</p>
    </div>
</footer>

<?php require_once 'includes/footer.php'; ?>