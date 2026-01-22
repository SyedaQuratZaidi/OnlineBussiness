<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Jenny's Cosmetics & Jewelry</title>
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
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contact.php">Contact</a>
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
<section class="page-header py-5 position-relative text-black" style="height: 100vh; font-family: 'Playfair Display', serif;">
    <!-- Background Image -->
    <img src="assets/images/Online Jewellery & Cosmetic Store.jpg" 
         class="position-absolute top-0 start-0 w-100 h-100" 
         style="object-fit: cover; z-index: -1;" alt="Contact Us">

    <div class="container h-100 d-flex flex-column justify-content-center position-relative">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title mb-3">Contact Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-dark text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">Contact</li>
                    </ol>
                </nav>
            </div>
  
    </div>
</section>

    <!-- Contact Section -->
    <section class="contact-section py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="section-title mb-3">Get In Touch</h2>
                    <p class="section-description">We'd love to hear from you! Send us a message and we'll respond as soon as possible.</p>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="contact-info-card text-center p-4 h-100">
                        <div class="contact-icon mb-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h5>Address</h5>
                        <p class="mb-0">Home Based Business<br>Lahore, Pakistan</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-info-card text-center p-4 h-100">
                        <div class="contact-icon mb-3">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h5>Phone</h5>
                        <p class="mb-0">
                            <a href="tel:+923227394199" class="text-decoration-none">+92 322 7394199</a>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-info-card text-center p-4 h-100">
                        <div class="contact-icon mb-3">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h5>Email</h5>
                        <p class="mb-0">
                            <a href="mailto:info@afjeweller.com" class="text-decoration-none">info@afjeweller.com</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="contact-form-card p-4">
                        <h3 class="mb-4 text-center">Send Us a Message</h3>
                        <form id="contactForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contactName" class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="contactName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contactEmail" class="form-label">Your Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="contactEmail" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contactPhone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="contactPhone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contactSubject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="contactSubject" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="contactMessage" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="contactMessage" rows="5" required></textarea>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="map-section bg-light p-4 rounded text-center">
                        <h4 class="mb-3"><i class="fas fa-map-marked-alt"></i> Find Us</h4>
                        <p class="text-muted mb-3">We are a home-based business operating from Lahore, Pakistan</p>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Modal -->
    <div class="modal fade" id="contactSuccessModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                    <h3>Message Sent Successfully!</h3>
                    <p class="text-muted">Thank you for contacting us. We'll get back to you soon.</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="footer-title">JENNY'S</h5>
                    <p>AJ, the true epitome of luxury jewels is providing high quality Jewelry which is first coated with Brass & then double coated with Silver. We are providing free shipping in gift packaging with 100% Money-Back Guarantee!</p>
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
