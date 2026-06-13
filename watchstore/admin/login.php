<?php
session_start();
require_once '../db/connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $admin = $crud->loginAdmin($username);

    if ($admin && md5($password) === $admin['password']) {
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_name'] = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Invalid username or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #1a1a2e; }
        .card { border-radius: 15px; border: none; }
        .password-wrapper { position: relative; }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            background: none;
            border: none;
            font-size: 18px;
            color: #666;
        }
        .toggle-password:focus { outline: none; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4 shadow">

                <h3 class="text-center mb-3">⌚ Admin Login</h3>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control"
                            placeholder="Enter username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password"
                                class="form-control" placeholder="Enter password" required>
                            <button type="button" class="toggle-password"
                                onclick="togglePass('password', this)">👁️</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-block">
                        Login as Admin
                    </button>
                </form>

                <p class="text-center mt-3">
                    <a href="../index.php">← Back to Store</a>
                </p>

            </div>
        </div>
    </div>
</div>

<script>
function togglePass(fieldId, btn) {
    let field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        btn.innerText = '🙈';
    } else {
        field.type = 'password';
        btn.innerText = '👁️';
    }
}
</script>

</body>
</html>