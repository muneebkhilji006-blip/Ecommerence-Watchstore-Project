<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../db/connection.php';

$success = '';
$error   = '';

// GET PRODUCT
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id         = $_GET['id'];
$product    = $crud->getProductById($id);
$categories = $crud->getAllCategories();

if (!$product) {
    header("Location: products.php");
    exit();
}

// UPDATE PRODUCT
if (isset($_POST['update_product'])) {
    $name        = trim($_POST['name']);
    $price       = trim($_POST['price']);
    $category_id = $_POST['category_id'];
    $image       = trim($_POST['image']);
    $description = trim($_POST['description']);

    if (empty($name) || empty($price)) {
        $error = 'Name and price are required!';
    } else {
        $result = $crud->updateProduct($id, $name, $price, $category_id, $image, $description);
        if ($result) {
            $success = 'Product updated successfully!';
            // Refresh product data
            $product = $crud->getProductById($id);
        } else {
            $error = 'Something went wrong. Please try again!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
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
        .preview-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
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

            <h2 class="mb-4">✏️ Edit Product</h2>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card p-4 shadow-sm">

                <!-- IMAGE PREVIEW -->
                <div class="text-center">
                    <img src="../<?php echo $product['image']; ?>"
                        class="preview-img" id="imagePreview">
                </div>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="<?php echo $product['name']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Price ($)</label>
                                <input type="number" name="price" class="form-control"
                                    value="<?php echo $product['price']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"
                                            <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo $cat['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Image Path</label>
                        <input type="text" name="image" id="imagePath" class="form-control"
                            value="<?php echo $product['image']; ?>"
                            oninput="updatePreview(this.value)">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo $product['description']; ?></textarea>
                    </div>

                    <button type="submit" name="update_product" class="btn btn-success">
                        💾 Save Changes
                    </button>
                    <a href="products.php" class="btn btn-secondary">
                        ← Back to Products
                    </a>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
function updatePreview(path) {
    document.getElementById('imagePreview').src = '../' + path;
}
</script>

</body>
</html>