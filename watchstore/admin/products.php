<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../db/connection.php';

$success = '';
$error   = '';

// DELETE PRODUCT
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $crud->deleteProduct($id);
    $success = 'Product deleted successfully!';
}

// ADD PRODUCT
if (isset($_POST['add_product'])) {
    $name        = trim($_POST['name']);
    $price       = trim($_POST['price']);
    $category_id = $_POST['category_id'];
    $image       = trim($_POST['image']);
    $description = trim($_POST['description']);

    if (empty($name) || empty($price)) {
        $error = 'Name and price are required!';
    } else {
        $result = $crud->addProduct($category_id, $name, $price, $image, $description);
        if ($result) {
            $success = 'Product added successfully!';
        } else {
            $error = 'Something went wrong. Please try again!';
        }
    }
}

// FETCH all products with category name
$products   = $crud->getAllProducts();
$categories = $crud->getAllCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
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
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar">
            <h4>⌚ Admin</h4>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="products.php" class="active">📦 Products</a>
            <a href="orders.php">🛒 Orders</a>
            <a href="users.php">👥 Users</a>
            <a href="../index.php">🏠 View Store</a>
            <a href="logout.php">🚪 Logout</a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-10 p-4">

            <h2 class="mb-4">📦 Manage Products</h2>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- ADD PRODUCT FORM -->
            <div class="card p-4 mb-4 shadow-sm">
                <h4 class="mb-3">➕ Add New Product</h4>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Luxury Watch Pro" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Price ($)</label>
                                <input type="number" name="price" class="form-control" placeholder="e.g. 999" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            <?php echo $cat['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Image Path</label>
                                <input type="text" name="image" class="form-control" placeholder="e.g. image/watch1.jpg">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Short product description..."></textarea>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-success">
                        ➕ Add Product
                    </button>
                </form>
            </div>

            <!-- PRODUCTS TABLE -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">All Products</h4>
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $p): ?>
                            <tr>
                                <td><?php echo $p['id']; ?></td>
                                <td>
                                    <img src="../<?php echo $p['image']; ?>" class="product-img">
                                </td>
                                <td><?php echo $p['name']; ?></td>
                                <td><?php echo $p['category_name']; ?></td>
                                <td>$<?php echo $p['price']; ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $p['id']; ?>"
                                        class="btn btn-warning btn-sm">✏️ Edit</a>
                                    <a href="products.php?delete=<?php echo $p['id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this product?')">
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