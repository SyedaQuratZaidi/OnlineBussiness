<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: user_login.php');
    exit();
}

$conn = db();
$user_id = (int)($_SESSION['user_id'] ?? 0);
$user_email = $_SESSION['user_email'] ?? '';
$user_name = $_SESSION['user_name'] ?? '';

$messages = [];

$editing = isset($_GET['edit']) && $_GET['edit'] === '1';

// Handle settings updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_email') {
        $new_email = trim($_POST['email'] ?? '');
        if ($new_email === '') {
            $messages[] = ['type' => 'danger', 'text' => 'Email cannot be empty.'];
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $messages[] = ['type' => 'danger', 'text' => 'Invalid email address.'];
        } else {
            // ensure email not used by another user
            $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1');
            $stmt->bind_param('si', $new_email, $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows > 0) {
                $messages[] = ['type' => 'danger', 'text' => 'Email already in use.'];
            } else {
                $stmt->close();
                $up = $conn->prepare('UPDATE users SET email = ? WHERE id = ?');
                $up->bind_param('si', $new_email, $user_id);
                if ($up->execute()) {
                    // update orders to keep association
                    $updOrders = $conn->prepare('UPDATE orders SET customer_email = ? WHERE customer_email = ?');
                    $updOrders->bind_param('ss', $new_email, $user_email);
                    $updOrders->execute();
                    $updOrders->close();

                    $_SESSION['user_email'] = $new_email;
                    $user_email = $new_email;
                    $messages[] = ['type' => 'success', 'text' => 'Email updated successfully.'];
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'Failed to update email.'];
                }
                $up->close();
            }
            $stmt->close();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $new2 = $_POST['new_password2'] ?? '';
        if ($new === '' || strlen($new) < 6) {
            $messages[] = ['type' => 'danger', 'text' => 'New password must be at least 6 characters.'];
        } elseif ($new !== $new2) {
            $messages[] = ['type' => 'danger', 'text' => 'Passwords do not match.'];
        } else {
            $stmt = $conn->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 1) {
                $row = $res->fetch_assoc();
                if (!password_verify($current, $row['password'])) {
                    $messages[] = ['type' => 'danger', 'text' => 'Current password is incorrect.'];
                } else {
                    $hash = password_hash($new, PASSWORD_DEFAULT);
                    $up = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $up->bind_param('si', $hash, $user_id);
                    if ($up->execute()) {
                        $messages[] = ['type' => 'success', 'text' => 'Password updated successfully.'];
                    } else {
                        $messages[] = ['type' => 'danger', 'text' => 'Failed to update password.'];
                    }
                    $up->close();
                }
            }
            $stmt->close();
        }
    }
}

// Fetch user orders
$orders = [];
if ($user_email) {
    $stmt = $conn->prepare('SELECT id, order_data, total, status, created_at FROM orders WHERE customer_email = ? ORDER BY created_at DESC');
    $stmt->bind_param('s', $user_email);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Jenny's</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8">
                <h2>My Account</h2>
                <p>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></p>
                <p><a href="user_logout.php" class="btn btn-outline-secondary">Logout</a></p>

                <?php foreach ($messages as $m): ?>
                    <div class="alert alert-<?php echo $m['type']; ?>"><?php echo htmlspecialchars($m['text']); ?></div>
                <?php endforeach; ?>

                <hr>
                <h4>Your Orders</h4>
                <?php if (empty($orders)): ?>
                    <p class="text-muted">You have not placed any orders yet.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($orders as $o): ?>
                            <?php $items = json_decode($o['order_data'], true); ?>
                            <div class="list-group-item mb-2">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>Order #<?php echo (int)$o['id']; ?></strong>
                                        <div class="small text-muted">Placed on <?php echo htmlspecialchars($o['created_at']); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <?php
                                            $status = $o['status'];
                                            $badgeClass = 'secondary';
                                            if ($status === 'pending' || $status === 'processing') $badgeClass = 'warning';
                                            if ($status === 'shipped') $badgeClass = 'info';
                                            if ($status === 'delivered') $badgeClass = 'success';
                                            if ($status === 'cancelled') $badgeClass = 'danger';
                                        ?>
                                        <span class="badge bg-<?php echo $badgeClass; ?> px-3 py-2"><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <?php if (is_array($items)): ?>
                                        <ul class="mb-1">
                                            <?php foreach ($items as $it): ?>
                                                <li><?php echo htmlspecialchars($it['name'] ?? 'Item'); ?> &times; <?php echo (int)($it['quantity'] ?? 1); ?> — ₨<?php echo number_format((float)($it['price'] ?? 0) * (int)($it['quantity'] ?? 1)); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="text-muted">Order items not available.</div>
                                    <?php endif; ?>
                                    <div><strong>Total:</strong> ₨<?php echo number_format((float)$o['total'], 2); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <h4>Settings</h4>
                <?php if ($editing): ?>
                    <div class="card mb-3 p-3">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_email">
                            <div class="mb-2">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-primary" type="submit">Update Email</button>
                                <a href="user_account.php" class="btn btn-link">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div class="card p-3">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_password">
                            <div class="mb-2">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="new_password2" class="form-control" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-secondary" type="submit">Change Password</button>
                                <a href="user_account.php" class="btn btn-link">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card p-3">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                        <a href="?edit=1" class="btn btn-primary">Edit Profile</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
