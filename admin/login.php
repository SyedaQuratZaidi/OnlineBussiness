<?php
session_start();

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Allowed admin accounts (username => password)
    $admins = [
        'admin' => 'admin123',
        'admin2' => 'admin123'
    ];

    if (isset($admins[$username]) && $password === $admins[$username]) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $login_error = 'Invalid username or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Jenny's Cosmetics & Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header text-center mb-4">
                <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
                <h2>Admin Login</h2>
                <p class="text-muted">Jenny's Cosmetics & Jewelry</p>
            </div>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required autocomplete="username">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <div class="text-center">
                    <a href="../index.php" class="text-muted text-decoration-none">
                        <i class="fas fa-arrow-left"></i> Back to Website
                    </a>
                </div>
            </form>
            <div id="loginError" class="alert alert-danger mt-3" style="display: <?php echo $login_error ? 'block' : 'none'; ?>;">
                <?php echo htmlspecialchars($login_error); ?>
            </div>
        </div>
            <div class="login-info mt-4 text-center text-white">
            <p class="mb-0">Default Credentials: admin / admin123 &nbsp; | &nbsp; admin2 / admin123</p>
        </div>
    </div>



</body>
</html>
