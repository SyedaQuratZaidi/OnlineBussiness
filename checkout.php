<?php
session_start();
require_once 'config.php';

// Database connection
$conn = db();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Calculate cart total
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_count += $item['quantity'];
}

// frontend user login flag
$user_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customerName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $cell_phone = trim($_POST['cellPhone'] ?? '');
    $work_phone = trim($_POST['workPhone'] ?? '');
    $date_of_birth = trim($_POST['dateOfBirth'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');

    if ($customer_name && $email && $address && $cell_phone) {
        // Prepare order data
        $order_data = json_encode($_SESSION['cart']);

        // Insert order into database
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, address, order_data, total, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("sssssd", $customer_name, $email, $cell_phone, $address, $order_data, $cart_total);

        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            $stmt->close();

            // Clear cart
            $_SESSION['cart'] = [];

            // Redirect to success page or show success message
            $message = 'Order placed successfully! Your order ID is #' . $order_id;
            $message_type = 'success';
        } else {
            $message = 'Error placing order: ' . $conn->error;
            $message_type = 'error';
        }
    } else {
        $message = 'Please fill in all required fields!';
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Jenny's Cosmetics & Jewelry</title>
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
                    <?php if ($user_logged_in): ?>
                        <a href="user_account.php" class="top-link"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'My Account'); ?></a>
                    <?php else: ?>
                        <a href="user_login.php" class="top-link"><i class="fas fa-user"></i> Login / Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item mega-menu">
                        <a class="nav-link dropdown-toggle" href="products.php" id="productsDropdown">
                            Products
                        </a>
                        <div class="mega-menu-content">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h6 class="mega-menu-title">Categories</h6>
                                        <ul class="mega-menu-list">
                                            <li class="mega-menu-category-item">
                                                <a href="#" class="mega-menu-category-link" data-category="cosmetics">
                                                    <i class="fas fa-palette"></i> Cosmetics
                                                </a>
                                                <div class="mega-submenu" id="cosmeticsSubmenu">
                                                    <ul class="mega-submenu-list">
                                                        <li><a href="products.php?category=complexion" class="mega-menu-link" data-subcategory="complexion" data-main-category="cosmetics">Complexion</a></li>
                                                        <li><a href="products.php?category=eyes" class="mega-menu-link" data-subcategory="eyes" data-main-category="cosmetics">Eyes</a></li>
                                                        <li><a href="products.php?category=lips" class="mega-menu-link" data-subcategory="lips" data-main-category="cosmetics">Lips</a></li>
                                                        <li><a href="products.php?category=nails" class="mega-menu-link" data-subcategory="nails" data-main-category="cosmetics">Nails</a></li>
                                                        <li><a href="products.php?category=skincare" class="mega-menu-link" data-subcategory="skincare" data-main-category="cosmetics">Skin Care</a></li>
                                                        <li><a href="products.php?category=fragrance" class="mega-menu-link" data-subcategory="fragrance" data-main-category="cosmetics">Fragrance</a></li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="mega-menu-category-item">
                                                <a href="#" class="mega-menu-category-link" data-category="jewelry">
                                                    <i class="fas fa-gem"></i> Jewelry
                                                </a>
                                                <div class="mega-submenu" id="jewelrySubmenu">
                                                    <ul class="mega-submenu-list">
                                                        <li><a href="products.php?category=anklets" class="mega-menu-link" data-subcategory="anklets" data-main-category="jewelry">Anklets</a></li>
                                                        <li><a href="products.php?category=bangles" class="mega-menu-link" data-subcategory="bangles" data-main-category="jewelry">Bangles</a></li>
                                                        <li><a href="products.php?category=bracelets" class="mega-menu-link" data-subcategory="bracelets" data-main-category="jewelry">Bracelets</a></li>
                                                        <li><a href="products.php?category=necklace" class="mega-menu-link" data-subcategory="necklace" data-main-category="jewelry">Necklace</a></li>
                                                        <li><a href="products.php?category=earrings" class="mega-menu-link" data-subcategory="earrings" data-main-category="jewelry">Ear Rings</a></li>
                                                        <li><a href="products.php?category=rings" class="mega-menu-link" data-subcategory="rings" data-main-category="jewelry">Rings</a></li>
                                                    </ul>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="mega-menu-products" id="productsDisplay">
                                            <p class="text-muted">Hover over a category to see products</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <form method="GET" action="products.php" class="search-box me-3 d-flex" role="search">
                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="cart.php" class="cart-icon position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge bg-danger cart-badge" id="cartBadge"><?php echo $cart_count; ?></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header py-4 bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Checkout Section -->
    <section class="checkout-section py-5">
        <div class="container">
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show mb-4" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <?php if ($message_type === 'success'): ?>
                <div class="mt-3">
                    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                </div>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (empty($_SESSION['cart'])): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h2 class="mb-3">Your cart is empty</h2>
                <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                <a href="products.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
            </div>
            <?php else: ?>
            <div class="row">
                <!-- Checkout Form -->
                <div class="col-lg-8 mb-4">
                    <div class="checkout-form-container">
                        <h3 class="checkout-title mb-4">Billing Information</h3>
                        <form id="checkoutForm" method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customerName" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="customerName" name="customerName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Shipping Address *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cellPhone" class="form-label">Cell Phone *</label>
                                    <input type="tel" class="form-control" id="cellPhone" name="cellPhone" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="workPhone" class="form-label">Work Phone</label>
                                    <input type="tel" class="form-control" id="workPhone" name="workPhone">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="dateOfBirth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <option value="jewelry">Jewelry</option>
                                        <option value="cosmetics">Cosmetics</option>
                                        <option value="fragrance">Fragrance</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="remarks" class="form-label">Additional Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Any special instructions or remarks..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h4 class="order-summary-title">Order Summary</h4>

                        <div class="order-items">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="order-item">
                                <div class="order-item-info">
                                    <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <span>Quantity: <?php echo $item['quantity']; ?></span>
                                </div>
                                <div class="order-item-price">
                                    ₨<?php echo number_format($item['price'] * $item['quantity']); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <div class="order-total">
                            <div class="d-flex justify-content-between">
                                <span><strong>Total:</strong></span>
                                <span><strong>₨<?php echo number_format($cart_total); ?></strong></span>
                            </div>
                        </div>

                        <button type="submit" form="checkoutForm" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-credit-card"></i> Place Order
                        </button>

                        <div class="payment-info mt-3">
                            <div class="payment-method">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure Payment</span>
                            </div>
                            <div class="payment-method">
                                <i class="fas fa-truck"></i>
                                <span>Free Shipping</span>
                            </div>
                            <div class="payment-method">
                                <i class="fas fa-undo"></i>
                                <span>Easy Returns</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="footer-title">JENNY'S</h5>
                    <p>Jenny's, the true epitome of beauty and elegance, is providing high quality cosmetics and imitation jewelry which is first coated with Brass & then double coated with Silver.</p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-title">Quick Navigation</h5>
                    <ul class="footer-links">
                        <li><a href="index.php#about">About Us</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Contact</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-phone"></i> +92 322 7394199</li>
                        <li><i class="fas fa-envelope"></i> info@jennys.com</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Subscribe Us</h5>
                    <form class="subscribe-form">
                        <input type="text" class="form-control mb-2" placeholder="Your Name">
                        <input type="email" class="form-control mb-2" placeholder="Your Email">
                        <button type="submit" class="btn btn-primary w-100">Subscribe</button>
                    </form>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; Jenny's 2025. Powered by Mindcob.com</p>
                </div>
            </div>
        </div>
    </footer>


</body>
</html>
<?php $conn->close(); ?>
