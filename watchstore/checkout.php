<?php
require_once 'includes/header.php';
require_once 'db/connection.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error   = '';

// PLACE ORDER
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $items   = json_decode($_POST['cart_data'], true);

    if (empty($items)) {
        $error = 'Your cart is empty!';
    } else {
        // Calculate total
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Insert order
$order_id = $crud->placeOrder($user_id, $total);

// Insert order items
foreach ($items as $item) {
    $product = $crud->getProductByName($item['name']);
    if ($product) {
        $crud->addOrderItem($order_id, $product['id'], $item['quantity'], $item['price']);
    }
}

        $success = 'Your order has been placed successfully! Order ID: #' . $order_id;
    }
}

// Fetch products for display
$products = $pdo->query("SELECT * FROM products")->fetchAll();
?>

<style>
    body { padding-top: 80px; background: #f4f4f4; }

    .checkout-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .cart-item-row {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding: 10px 0;
    }

    .cart-item-row img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 15px;
    }

    footer { background: #343a40; color: white; padding: 20px 0; }
</style>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand font-weight-bold" href="index.php">⌚ WatchStore</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="index.php" class="btn btn-outline-light">← Back to Store</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">

    <h2 class="mb-4">🛒 Checkout</h2>

    <?php if($success): ?>
        <div class="alert alert-success text-center">
            <h4><?php echo $success; ?></h4>
            <a href="index.php" class="btn btn-primary mt-2">Continue Shopping</a>
            <a href="my_orders.php" class="btn btn-success mt-2">View My Orders</a>
        </div>
    <?php elseif($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php else: ?>

    <div class="row">

        <!-- ORDER SUMMARY -->
        <div class="col-md-8">
            <div class="card checkout-card p-4 mb-4">
                <h4 class="mb-3">Order Summary</h4>
                <div id="checkoutItems">
                    <p class="text-muted">Your cart is empty. <a href="index.php">Go shopping!</a></p>
                </div>
                <hr>
                <h5>Total: $<span id="checkoutTotal">0</span></h5>
            </div>
        </div>

        <!-- CUSTOMER INFO -->
        <!-- CUSTOMER INFO -->
<div class="col-md-4">
    <div class="card checkout-card p-4">
        <h4 class="mb-3">Customer Info</h4>
        <p><strong>Name:</strong> <?php echo $_SESSION['user_name']; ?></p>
        <p><strong>Email:</strong> <?php echo $_SESSION['user_email']; ?></p>
        <p><strong>Phone:</strong> <?php echo $_SESSION['user_phone']; ?></p>
        <p><strong>Address:</strong> <?php echo $_SESSION['user_address']; ?></p>
        <hr>
        <form method="POST" id="checkoutForm">
            <input type="hidden" name="cart_data" id="cartDataInput">
            <button type="submit" class="btn btn-success btn-block btn-lg" id="placeOrderBtn">
                ✅ Place Order
            </button>
        </form>
        <a href="index.php" class="btn btn-outline-secondary btn-block mt-2">
            ← Continue Shopping
        </a>
    </div>
</div>

    </div>

    <?php endif; ?>

</div>

<!-- FOOTER -->
<footer class="mt-5">
    <div class="container text-center">
        <p>⌚ WatchStore &copy; 2026</p>
    </div>
</footer>

<script>
// Load cart from localStorage
document.addEventListener('DOMContentLoaded', function() {
    let cart = JSON.parse(localStorage.getItem('watchstore_cart')) || [];
    let checkoutItems = document.getElementById('checkoutItems');
    let checkoutTotal = document.getElementById('checkoutTotal');
    let cartDataInput = document.getElementById('cartDataInput');

    if (!checkoutItems) return;

    if (cart.length === 0) {
        checkoutItems.innerHTML = '<p class="text-muted">Your cart is empty. <a href="index.php">Go shopping!</a></p>';
        return;
    }

    let html  = '';
    let total = 0;

    cart.forEach(item => {
        total += item.price * item.quantity;
        html  += `
        <div class="cart-item-row">
            <img src="${item.image}" alt="${item.name}">
            <div style="flex:1">
                <h6 class="mb-0">${item.name}</h6>
                <small class="text-muted">Qty: ${item.quantity}</small>
            </div>
            <strong>$${(item.price * item.quantity).toFixed(2)}</strong>
        </div>`;
    });

    checkoutItems.innerHTML = html;
    checkoutTotal.innerText = total.toFixed(2);
    cartDataInput.value     = JSON.stringify(cart);
});

// Clear cart after order placed
<?php if($success): ?>
    localStorage.removeItem('watchstore_cart');
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>
