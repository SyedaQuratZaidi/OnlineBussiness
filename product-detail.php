<?php
session_start();
require_once 'config.php';

// Database connection
$conn = db();

// Cart count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
// frontend user login flag
$user_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

$product_id = intval($_GET['id'] ?? 0);
$product = null;

if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
}

if (!$product) {
    header('Location: products.php');
    exit();
}

// Get related products (same category, different id)
$related_products = [];
$stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$stmt->bind_param("si", $product['category'], $product_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $related_products[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Jenny's Cosmetics & Jewelry</title>
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
                        <span class="badge bg-danger cart-badge" id="cartBadge"><?php echo (int)$cart_count; ?></span>
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
                    <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Product Detail Section -->
    <section class="product-detail-section py-5">
        <div class="container">
            <div class="row">
                <!-- Product Image -->
                <div class="col-lg-6 mb-4">
                    <div class="product-detail-image">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded shadow">
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-detail-info">
                        <div class="product-category mb-2"><?php echo htmlspecialchars(ucfirst($product['subcategory'])); ?></div>
                        <h1 class="product-detail-name mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <div class="product-detail-price mb-4">₨<?php echo number_format($product['price']); ?></div>

                        <?php if ($product['description']): ?>
                        <p class="product-detail-description mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                        <?php endif; ?>

                        <div class="product-features mb-4">
                            <h5>Features:</h5>
                            <ul>
                                <li>Premium Quality</li>
                                <li>Free Shipping</li>
                                <li>100% Money-Back Guarantee</li>
                                <li>Gift Packaging Included</li>
                            </ul>
                        </div>

                        <div class="quantity-selector mb-4">
                            <form method="POST" action="cart.php" class="d-inline">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <label class="form-label me-2">Quantity:</label>
                                <input type="number" name="quantity" id="productQuantity" class="quantity-input me-2" value="1" min="1">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>

                        <div class="product-actions-detail">
                            <form method="POST" action="cart.php" class="d-inline">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary btn-lg me-2">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </form>
                            <a href="products.php" class="btn btn-outline-secondary btn-lg">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products Section -->
    <?php if (!empty($related_products)): ?>
    <section class="related-products-section py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5">Related Products</h2>
            <div class="row g-4">
                <?php foreach ($related_products as $related): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="product-card" data-id="<?php echo (int)$related['id']; ?>" role="button" tabindex="0">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>" loading="lazy">
                            <span class="product-badge"><?php echo htmlspecialchars(ucfirst($related['category'])); ?></span>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?php echo htmlspecialchars($related['subcategory']); ?></div>
                            <h3 class="product-name"><?php echo htmlspecialchars($related['name']); ?></h3>
                            <div class="product-price">₨<?php echo number_format($related['price']); ?></div>
                            <div class="product-actions">
                                <form method="POST" action="cart.php" class="d-inline">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $related['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add-cart">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                </form>
                                <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="btn-view">View</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

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
