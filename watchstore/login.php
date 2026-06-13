<?php
require_once 'includes/header.php';
require_once 'db/connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'All fields are required!';
    } else {
        $user = $crud->loginUser($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['user_name']    = $user['full_name'];
            $_SESSION['user_email']   = $user['email'];
            $_SESSION['user_phone']   = $user['phone'];
            $_SESSION['user_address'] = $user['address'];
            header("Location: index.php");
            exit();
        } else {
            $error = 'Invalid email or password!';
        }
    }
}
?>

<style>
    body { background: #f0f2f5; padding-top: 60px; }
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

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 shadow">

                <h3 class="text-center mb-3">Login</h3>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control"
                            placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password"
                                class="form-control" placeholder="Enter your password" required>
                            <button type="button" class="toggle-password"
                                onclick="togglePass('password', this)">👁️</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>

                <p class="text-center mt-3">
                    Don't have an account? <a href="signup.php">Sign up here</a>
                </p>
                <a href="index.php" class="btn btn-link btn-block text-center">
                    ← Back to Home
                </a>

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

<?php require_once 'includes/footer.php'; ?>