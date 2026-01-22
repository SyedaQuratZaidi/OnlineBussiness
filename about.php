<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Jenny's Cosmetics & Jewelry</title>
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
                    <a href="#" class="top-link"><i class="fas fa-user"></i> My Account</a>
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
                        <a class="nav-link active" href="about.php">About Us</a>
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
                        <span class="badge bg-danger cart-badge" id="cartBadge">0</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
 <section class="about-hero-section position-relative text-black" style="height: 100vh; font-family: 'Playfair Display', serif;">
    <!-- Background Image -->
    <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=1600&h=900&fit=crop" 
         class="w-100 h-100 position-absolute top-0 start-0" 
         style="object-fit: cover; z-index: -1;" alt="About Us">

    <!-- Overlay for subtle contrast -->
    <div class="overlay position-absolute top-0 start-0 w-100 h-100" 
         style="background-color: rgba(255,255,255,0.2); z-index: 0;"></div>

    <!-- Hero Content -->
    <div class="container h-100 d-flex flex-column justify-content-center position-relative" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0 hero-fade">
                <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600&h=400&fit=crop" 
                     alt="About Us" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6 hero-fade">
                <h1 class="page-title mb-3">About Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-dark text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">About Us</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

    <!-- About Section -->
    <section class="about-section py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="assets/images/imgs/about.jpg" alt="Our Story" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title text-start mb-4">Our Story</h2>
                    <p class="lead">Welcome to AFJEWELLER - Your Trusted Online Destination for Premium Jewelry & Cosmetics</p>
                    <p>Jenny's, the true epitome of beauty and elegance, started as a home-based business with a passion for providing high-quality cosmetics and imitation jewelry. What began as a small venture serving friends and family has grown into a trusted online store.</p>
                    <p>We specialize in providing high-quality artificial jewelry which is first coated with Brass & then double coated with Silver, ensuring lasting shine and durability. Our commitment to quality and customer satisfaction has made us a preferred choice for jewelry and cosmetics enthusiasts.</p>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-4 mb-4">
                    <div class="about-feature-card text-center p-4 h-100">
                        <div class="feature-icon-large mb-3">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h4>Premium Quality</h4>
                        <p>Our jewelry is double coated with Silver for lasting shine and durability. We ensure every piece meets our high quality standards.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="about-feature-card text-center p-4 h-100">
                        <div class="feature-icon-large mb-3">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h4>Free Shipping</h4>
                        <p>We provide free shipping on all orders with beautiful gift packaging. Your order will arrive safely and beautifully wrapped.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="about-feature-card text-center p-4 h-100">
                        <div class="feature-icon-large mb-3">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>100% Guarantee</h4>
                        <p>We offer a 100% Money-Back Guarantee on all our products. Your satisfaction is our top priority.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="about-content-card p-5">
                        <h2 class="section-title text-center mb-4">Why Choose Us?</h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="check-icon me-3">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <h5>Wide Range of Products</h5>
                                        <p>From elegant jewelry pieces to premium cosmetics, we offer a diverse collection to meet all your beauty needs.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="check-icon me-3">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <h5>Affordable Prices</h5>
                                        <p>We believe in providing quality products at affordable prices, making luxury accessible to everyone.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="check-icon me-3">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <h5>Customer Support</h5>
                                        <p>Our dedicated customer support team is always ready to assist you with any queries or concerns.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="check-icon me-3">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <h5>Secure Shopping</h5>
                                        <p>Your personal information and payment details are safe with us. We ensure secure transactions.</p>
                                    </div>
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
                    <p>Jenny's, the true epitome of beauty and elegance, is providing high quality cosmetics and imitation jewelry which is first coated with Brass & then double coated with Silver. We are providing free shipping in gift packaging with 100% Money-Back Guarantee!</p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-title">Quick Navigation</h5>
                    <ul class="footer-links">
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Contact</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-phone"></i> +92 322 7394199</li>
                        <li><i class="fas fa-envelope"></i> info@afjeweller.com</li>
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
