<?php
require_once 'includes/header.php';
require_once 'db/connection.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];

    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($password)) {
        $error = 'All fields are required!';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match!';
    } elseif ($crud->emailExists($email)) {
        $error = 'Email already registered!';
    } else {
        $result = $crud->registerUser($full_name, $email, $phone, $address, $password);
        if ($result) {
            $success = 'Account created successfully! You can now login.';
        } else {
            $error = 'Something went wrong. Please try again!';
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

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow">

                <h3 class="text-center mb-3">Create Account</h3>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control"
                            placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control"
                            placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control"
                            placeholder="e.g. +92 300 1234567" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="2"
                            placeholder="Enter your full address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password"
                                class="form-control" placeholder="Create password" required>
                            <button type="button" class="toggle-password"
                                onclick="togglePass('password', this)">👁️</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="confirm_password" id="confirm_password"
                                class="form-control" placeholder="Repeat password" required>
                            <button type="button" class="toggle-password"
                                onclick="togglePass('confirm_password', this)">👁️</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">
                        Create Account
                    </button>
                </form>

                <p class="text-center mt-3">
                    Already have an account? <a href="login.php">Login here</a>
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