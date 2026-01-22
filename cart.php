<?php
session_start();
require_once 'config.php';

// Database connection
$conn = db();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        // require login for cart operations
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: user_login.php');
            exit();
        }
        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);

        if ($product_id > 0 && $quantity > 0) {
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                $found = false;
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $product_id) {
                        $item['quantity'] += $quantity;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $_SESSION['cart'][] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => $quantity
                    ];
                }
            }
            $stmt->close();
        }
        header('Location: cart.php');
        exit();
    } elseif ($action === 'update') {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: user_login.php');
            exit();
        }
        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        if ($product_id > 0) {
            if ($quantity <= 0) {
                $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($product_id) {
                    return $item['id'] != $product_id;
                });
            } else {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $product_id) {
                        $item['quantity'] = $quantity;
                        break;
                    }
                }
            }
        }
        header('Location: cart.php');
        exit();
    } elseif ($action === 'remove') {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: user_login.php');
            exit();
        }
        $product_id = intval($_POST['product_id'] ?? 0);
        if ($product_id > 0) {
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($product_id) {
                return $item['id'] != $product_id;
            });
        }
        header('Location: cart.php');
        exit();
    } elseif ($action === 'clear') {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: user_login.php');
            exit();
        }
        $_SESSION['cart'] = [];
        header('Location: cart.php');
        exit();
    }
}

// Calculate totals
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_count += $item['quantity'];
}
// user login flag for frontend
$user_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Jenny's Cosmetics & Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <a href="tel:+923227394199" class="top-link"><i class="fas fa-phone"></i> +92 322 7394199</a>
            </div>
            <div class="col-md-6 text-end">
                <a href="admin/login.php" class="top-link"><i class="fas fa-user-shield"></i> Admin Login</a>
            </div>
        </div>
    </div>
</div>

<!-- Navigation -->
<?php
// Include a project navbar if present; otherwise render fallback navigation
if (file_exists(__DIR__ . '/navbar.php')) {
    include 'navbar.php';
} else {
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <span class="brand-text">Jenny's</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                            <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                            <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="cart.php" class="cart-icon position-relative me-3"><i class="fas fa-shopping-cart"></i></a>
                    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                        <a href="user_account.php" class="btn btn-sm btn-outline-secondary"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                    <?php else: ?>
                        <a href="user_login.php" class="btn btn-sm btn-primary">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <?php
}
?>

<!-- Hero Section (full-width, text on left, black text) -->
<section class="page-header position-relative text-black" style="height: 60vh;">
    <img src="https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1600&h=900&fit=crop" 
         class="position-absolute top-0 start-0 w-100 h-100" 
         style="object-fit: cover; z-index: -1;" alt="Cart Hero">
    <div class="container h-100 d-flex flex-column justify-content-center position-relative">
        <h1 class="display-3 fw-bold">Shopping Cart</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-dark text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page">Cart</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Cart Section -->
<section class="cart-section py-5">
    <div class="container">
        <?php if (!$user_logged_in): ?>
            <div class="alert alert-info">Please <a href="user_login.php">login</a> or <a href="register.php">register</a> to add items to your cart and proceed to checkout.</div>
        <?php endif; ?>

        <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h2>Your cart is empty</h2>
            <p class="text-muted">Looks like you haven't added any items yet.</p>
            <a href="products.php" class="btn btn-primary btn-lg"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
        </div>
        <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="cart-items">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="cart-item d-flex align-items-center mb-4 p-3 rounded shadow-sm bg-white">
                        <div class="cart-item-image me-3">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid rounded" style="max-width: 120px;">
                        </div>
                        <div class="cart-item-details flex-grow-1">
                            <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="mb-2">₨<?php echo number_format($item['price']); ?></p>
                            <div class="d-flex align-items-center">
                                <form method="POST" class="d-inline me-2">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="quantity" value="<?php echo max(1, $item['quantity'] - 1); ?>">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">-</button>
                                </form>
                                <form method="POST" id="quantityForm-<?php echo $item['id']; ?>" class="d-inline me-2">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control form-control-sm quantity-input" style="width: 70px; display: inline-block; vertical-align: middle;">
                                </form>
                                <form method="POST" class="d-inline ms-2">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">+</button>
                                </form>
                                <form method="POST" class="d-inline ms-2">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i> Remove</button>
                                </form>
                            </div>
                        </div>
                        <div class="cart-item-total ms-3">
                            <h5>₨<?php echo number_format($item['price'] * $item['quantity']); ?></h5>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="products.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                    <form method="POST">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Clear Cart</button>
                    </form>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="cart-summary p-4 rounded shadow-sm bg-white">
                    <h4 class="mb-3">Order Summary</h4>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (<?php echo $cart_count; ?> items)</span>
                        <span>₨<?php echo number_format($cart_total); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong>₨<?php echo number_format($cart_total); ?></strong>
                    </div>
                    <?php if ($user_logged_in): ?>
                        <a href="checkout.php" class="btn btn-primary w-100"><i class="fas fa-credit-card"></i> Proceed to Checkout</a>
                    <?php else: ?>
                        <a href="user_login.php" class="btn btn-primary w-100"><i class="fas fa-sign-in-alt"></i> Login to Checkout</a>
                    <?php endif; ?>
                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2"><i class="fas fa-shield-alt me-2"></i> Secure Checkout</div>
                        <div class="d-flex align-items-center mb-2"><i class="fas fa-truck me-2"></i> Free Shipping</div>
                        <div class="d-flex align-items-center"><i class="fas fa-undo me-2"></i> Easy Returns</div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Footer -->
<?php include 'footer.php'; ?> <!-- Keeps your theme footer consistent -->

</body>
</html>
<?php $conn->close(); ?>
