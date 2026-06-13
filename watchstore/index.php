<?php
require_once 'includes/header.php';
require_once 'db/connection.php';

// Fetch products by category
$luxury  = $crud->getProductsByCategory(1);
$sports  = $crud->getProductsByCategory(2);
$classic = $crud->getProductsByCategory(3);
?>

<style>
    html { scroll-behavior: smooth; }

    body {
        background: #f4f4f4;
        overflow-x: hidden;
        padding-top: 80px;
    }

    body.dark-mode { background: #121212; color: white; }
    body.dark-mode .card { background: #1e1e1e; color: white; }
    body.dark-mode .navbar { background: black !important; }
    body.dark-mode #home { background: #181818; }
    body.dark-mode .cart-sidebar { background: #1e1e1e; color: white; }
    body.dark-mode .menu-sidebar { background: #1e1e1e; }
    body.dark-mode .menu-sidebar a { background: #333; color: white; }

    #home { background: #093a6b; padding: 40px 0; }

    footer { background: #343a40; color: white; padding: 20px 0; }

    .card {
        margin-top: 20px;
        transition: 0.3s;
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    .card-img-top {
        height: 200px;
        object-fit: cover;
    }

    .carousel-item img { height: 500px; object-fit: cover; }

    h2 { color: white; margin-top: 40px; margin-bottom: 20px; }

    .price { color: green; font-weight: bold; font-size: 18px; }

    .overlay {
        position: fixed; top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        display: none; z-index: 999;
    }
    .overlay.active { display: block; }

    .menu-sidebar {
        position: fixed; top: 0; left: -300px;
        width: 260px; height: 100%;
        background: white;
        box-shadow: 5px 0 20px rgba(0,0,0,0.3);
        transition: 0.4s; z-index: 9999; padding: 20px;
    }
    .menu-sidebar.active { left: 0; }

    .menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .menu-header button {
        border: none; background: red;
        color: white; padding: 5px 10px; border-radius: 5px;
    }
    .menu-sidebar a {
        display: block; padding: 15px;
        margin-bottom: 15px; background: #f4f4f4;
        color: black; text-decoration: none;
        border-radius: 10px; font-weight: bold; transition: 0.3s;
    }
    .menu-sidebar a:hover { background: #007bff; color: white; }

    .cart-sidebar {
        position: fixed; top: 0; right: -420px;
        width: 400px; height: 100%;
        background: white;
        box-shadow: -5px 0 20px rgba(0,0,0,0.3);
        transition: 0.4s; z-index: 9999;
        padding: 20px; overflow-y: auto;
    }
    .cart-sidebar.active { right: 0; }

    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .cart-header button {
        border: none; background: red;
        color: white; padding: 5px 12px; border-radius: 5px;
    }

    .cart-item {
        display: flex; align-items: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 15px; margin-bottom: 15px;
    }
    .cart-item img {
        width: 70px; height: 70px;
        object-fit: cover; border-radius: 10px; margin-right: 10px;
    }

    .quantity-controls { display: flex; align-items: center; gap: 8px; }
    .quantity-controls button {
        border: none; background: black;
        color: white; width: 30px; height: 30px; border-radius: 5px;
    }

    .cart-footer { margin-top: 20px; }
</style>

<!-- OVERLAY -->
<div class="overlay" id="overlay" onclick="closeAllSidebars()"></div>

<!-- MENU SIDEBAR -->
<div id="menuSidebar" class="menu-sidebar">
    <div class="menu-header">
        <h4>Categories</h4>
        <button onclick="closeMenu()">✖</button>
    </div>
    <a href="#luxury" onclick="closeMenu()">Luxury Watches</a>
    <a href="#sports" onclick="closeMenu()">Sports Watches</a>
    <a href="#classic" onclick="closeMenu()">Classic Watches</a>
</div>

<!-- CART SIDEBAR -->
<div id="cartSidebar" class="cart-sidebar">
    <div class="cart-header">
        <h4>Your Cart</h4>
        <button onclick="closeCart()">✖</button>
    </div>
    <div id="cartItems"><p>Your cart is empty</p></div>
    <div class="cart-footer">
        <h5>Total: $<span id="cartTotal">0</span></h5>
        <button class="btn btn-success btn-block" onclick="placeOrder()">Place Order</button>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">

    <button class="btn btn-outline-light mr-3" onclick="openMenu()">☰</button>

    <a class="navbar-brand font-weight-bold" href="index.php">⌚ WatchStore</a>

    <button class="navbar-toggler" type="button"
        data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto align-items-lg-center">

            <li class="nav-item">
                <a class="nav-link" href="index.php">🏠 Home</a>
            </li>

            <li class="nav-item ml-lg-2">
                <button class="btn btn-warning" onclick="openCart()">
                    🛒 Cart <span id="cartCount">(0)</span>
                </button>
            </li>

            <?php if(isset($_SESSION['user_id'])): ?>
    <li class="nav-item ml-lg-2 mt-2 mt-lg-0">
        <span class="btn btn-outline-light">
            👤 <?php echo $_SESSION['user_name']; ?>
        </span>
    </li>
    <li class="nav-item ml-lg-2 mt-2 mt-lg-0">
        <a href="my_orders.php" class="btn btn-info">📦 My Orders</a>
    </li>
    <li class="nav-item ml-lg-2 mt-2 mt-lg-0">
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </li>
            <?php else: ?>
                <li class="nav-item ml-lg-2 mt-2 mt-lg-0">
                    <a href="login.php" class="btn btn-outline-light">Login</a>
                </li>
                <li class="nav-item ml-lg-2 mt-2 mt-lg-0">
                    <a href="signup.php" class="btn btn-primary">Sign Up</a>
                </li>
            <?php endif; ?>

            <li class="nav-item ml-lg-2 mt-2 mt-lg-0">
                <button class="btn btn-outline-light" onclick="toggleTheme()">🌓</button>
            </li>

        </ul>
    </div>
</nav>

<!-- SLIDER -->
<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="image/Cbar.jpg" class="d-block w-100">
        </div>
        <div class="carousel-item">
            <img src="image/Lbar.jpg" class="d-block w-100">
        </div>
        <div class="carousel-item">
            <img src="image/Sbar.jpg" class="d-block w-100">
        </div>
    </div>
</div>

<!-- PRODUCTS -->
<section id="home" class="container-fluid">

    <!-- LUXURY -->
    <div class="container" id="luxury">
        <h2 class="text-center">Luxury Watches</h2>
        <div class="row">
            <?php foreach($luxury as $product): ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="<?php echo $product['image']; ?>" class="card-img-top">
                    <div class="card-body">
                        <h5><?php echo $product['name']; ?></h5>
                        <p class="price">$<?php echo $product['price']; ?></p>
                        <button class="btn btn-primary btn-block"
                            onclick="addToCart('<?php echo $product['name']; ?>',
                                <?php echo $product['price']; ?>,
                                '<?php echo $product['image']; ?>')">
                            Add To Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SPORTS -->
    <div class="container" id="sports">
        <h2 class="text-center">Sports Watches</h2>
        <div class="row">
            <?php foreach($sports as $product): ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="<?php echo $product['image']; ?>" class="card-img-top">
                    <div class="card-body">
                        <h5><?php echo $product['name']; ?></h5>
                        <p class="price">$<?php echo $product['price']; ?></p>
                        <button class="btn btn-primary btn-block"
                            onclick="addToCart('<?php echo $product['name']; ?>',
                                <?php echo $product['price']; ?>,
                                '<?php echo $product['image']; ?>')">
                            Add To Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CLASSIC -->
    <div class="container" id="classic">
        <h2 class="text-center">Classic Watches</h2>
        <div class="row">
            <?php foreach($classic as $product): ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="<?php echo $product['image']; ?>" class="card-img-top">
                    <div class="card-body">
                        <h5><?php echo $product['name']; ?></h5>
                        <p class="price">$<?php echo $product['price']; ?></p>
                        <button class="btn btn-primary btn-block"
                            onclick="addToCart('<?php echo $product['name']; ?>',
                                <?php echo $product['price']; ?>,
                                '<?php echo $product['image']; ?>')">
                            Add To Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</section>

<!-- FOOTER -->
<footer>
    <div class="container text-center">
        <h4>Contact Us</h4>
        <p>Email: muneebkhilji006@gmail.com</p>
        <p>Phone: +92 304 9992079</p>
    </div>
</footer>

<!-- JAVASCRIPT -->
<script>
let cart = [];

function addToCart(name, price, image) {
    let existing = cart.find(i => i.name === name);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ name, price, image, quantity: 1 });
    }
    updateCart();
}

function updateCart() {
    let cartItems = document.getElementById('cartItems');
    let cartTotal = document.getElementById('cartTotal');
    let cartCount = document.getElementById('cartCount');

    cartItems.innerHTML = '';

    if (cart.length === 0) {
        cartItems.innerHTML = '<p>Your cart is empty</p>';
        cartTotal.innerText = 0;
        cartCount.innerText = '(0)';
        return;
    }

    let total = 0, totalItems = 0;

    cart.forEach((item, index) => {
        total += item.price * item.quantity;
        totalItems += item.quantity;
        cartItems.innerHTML += `
        <div class="cart-item">
            <img src="${item.image}">
            <div style="flex:1;">
                <h6>${item.name}</h6>
                <p>$${item.price}</p>
                <div class="quantity-controls">
                    <button onclick="decreaseQuantity(${index})">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="increaseQuantity(${index})">+</button>
                </div>
            </div>
        </div>`;
    });

    cartTotal.innerText = total;
    cartCount.innerText = '(' + totalItems + ')';
}

function increaseQuantity(index) { cart[index].quantity++; updateCart(); }

function decreaseQuantity(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
    } else {
        cart.splice(index, 1);
    }
    updateCart();
}

function placeOrder() {
    if (cart.length === 0) {
        alert('Cart is empty!');
    } else {
        localStorage.setItem('watchstore_cart', JSON.stringify(cart));
        window.location.href = 'checkout.php';
    }
}

function openCart()  { document.getElementById('cartSidebar').classList.add('active'); document.getElementById('overlay').classList.add('active'); }
function closeCart() { document.getElementById('cartSidebar').classList.remove('active'); document.getElementById('overlay').classList.remove('active'); }
function openMenu()  { document.getElementById('menuSidebar').classList.add('active'); document.getElementById('overlay').classList.add('active'); }
function closeMenu() { document.getElementById('menuSidebar').classList.remove('active'); document.getElementById('overlay').classList.remove('active'); }
function closeAllSidebars() { closeCart(); closeMenu(); }
function toggleTheme() { document.body.classList.toggle('dark-mode'); }
</script>

<?php require_once 'includes/footer.php'; ?>