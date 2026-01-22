<?php
session_start();
require_once 'config.php';

$conn = db();

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
// frontend user login flag
$user_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

$featured_products = [];
$result = $conn->query("SELECT * FROM products WHERE id IN (1, 11, 2, 3, 4, 5) ORDER BY FIELD(id, 1, 11, 2, 3, 4, 5)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $featured_products[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jenny's Cosmetics & Jewelry - Premium Beauty & Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
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
                    <li class="nav-item active">
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

    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="hero-slide hero-slide-1 d-flex align-items-center">
                        <div class="container">
                            <div class="row align-items-center min-vh-75">
                                <div class="col-lg-6 mb-5 mb-lg-0 hero-content">
                                    <h1 class="hero-title">Premium Jewelry & Cosmetics</h1>
                                    <p class="hero-subtitle">Discover our exquisite collection of handcrafted jewelry and premium beauty products. Quality craftsmanship meets elegant design.</p>
                                    <div class="hero-buttons">
                                        <a href="products.php" class="hero-btn">
                                            <i class="fas fa-shopping-bag"></i> Shop Now
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <!-- background image applied by .hero-slide-1 -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="hero-slide hero-slide-2 d-flex align-items-center">
                        <div class="container">
                            <div class="row align-items-center min-vh-75">
                                <div class="col-lg-6 mb-5 mb-lg-0 hero-content">
                                    <h1 class="hero-title">Explore New Arrivals</h1>
                                    <p class="hero-subtitle">Fresh styles and exclusive offers.</p>
                                    <div class="hero-buttons">
                                        <a href="products.php" class="hero-btn">
                                            <i class="fas fa-shopping-bag"></i> Shop Now
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <!-- background image applied by .hero-slide-2 -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <section class="categories-section py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5">Shop By Category</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <a href="products.php?category=jewelry" class="category-card">
                        <div class="category-image">
                            <img src="assets/images/jewlarry.jpg" alt="Jewelry">
                        </div>
                        <div class="category-overlay">
                            <h3>Jewelry</h3>
                            <p>Explore Collection</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="products.php?category=cosmetics" class="category-card">
                        <div class="category-image">
                            <img src="assets/images/cosmetic.webp" alt="Cosmetics">
                        </div>
                        <div class="category-overlay">
                            <h3>Cosmetics</h3>
                            <p>Explore Collection</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="products.php?category=fragrance" class="category-card">
                        <div class="category-image">
                            <img src="assets/images/fragnace.jpg" alt="Fragrance">
                        </div>
                        <div class="category-overlay">
                            <h3>Fragrance</h3>
                            <p>Explore Collection</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-products-section py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">Featured Products</h2>
            <div class="row g-4" id="featuredProducts">
                <?php foreach ($featured_products as $product): ?>
                <div class="col-md-6 col-lg-4 mb-4 fade-in">
                    <div class="product-card" data-id="<?php echo htmlspecialchars($product['id']); ?>" role="button" tabindex="0">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                            <span class="product-badge"><?php echo htmlspecialchars(ucfirst($product['category'])); ?></span>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?php echo htmlspecialchars($product['subcategory']); ?></div>
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-price">₨<?php echo number_format($product['price']); ?></div>
                            <div class="product-actions">
                                <form method="POST" action="cart.php" class="d-inline">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add-cart">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                </form>
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn-view">View</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="products.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-eye"></i> View All Products
                </a>
            </div>
        </div>
    </section>

    <section class="about-preview-section py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&h=600&fit=crop" alt="Our Story" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title mb-4">Why Choose Jenny's?</h2>
                    <p class="lead">Jenny's, the true epitome of beauty and elegance, started as a home-based business with a passion for providing high-quality cosmetics and imitation jewelry.</p>
                    <p>Our commitment to quality and customer satisfaction has made us a preferred choice for jewelry and cosmetics enthusiasts.</p>
                    <div class="features-list mt-4">
                        <div class="feature-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Premium Quality Products</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Free Shipping</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>100% Money-Back Guarantee</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Gift Packaging Included</span>
                        </div>
                    </div>
                    <a href="about.php" class="btn btn-primary mt-4">
                        <i class="fas fa-info-circle"></i> Learn More About Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-section py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">What Our Customers Say</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"Amazing quality jewelry and cosmetics! The products exceeded my expectations. Fast shipping and excellent customer service."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1494790108755-2616b612b5c5?w=100&h=100&fit=crop&crop=face" alt="Customer" class="testimonial-avatar">
                            <div>
                                <h5>Sarah Johnson</h5>
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"I've been shopping with Jenny's for over a year now. Their jewelry collection is stunning and the cosmetics are top-notch quality."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face" alt="Customer" class="testimonial-avatar">
                            <div>
                                <h5>Michael Chen</h5>
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"Beautiful craftsmanship and attention to detail. The necklace I ordered was perfect and arrived well-packaged. Highly recommended!"</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face" alt="Customer" class="testimonial-avatar">
                            <div>
                                <h5>Emma Davis</h5>
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                        <li><a href="about.php">About Us</a></li>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
<?php $conn->close(); ?>
