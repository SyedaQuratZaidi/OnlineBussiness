<?php
session_start();
require_once __DIR__ . '/config.php';

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

$categoryParam = isset($_GET['category']) ? strtolower(trim($_GET['category'])) : '';
$searchParam = isset($_GET['search']) ? trim($_GET['search']) : '';
// Price filter (GET)
$minPrice = isset($_GET['min_price']) ? trim($_GET['min_price']) : '';
$maxPrice = isset($_GET['max_price']) ? trim($_GET['max_price']) : '';

$topCategories = ['jewelry', 'cosmetics', 'fragrance'];
$knownSubcategories = [
	'complexion','eyes','lips','nails','skincare',
	'anklets','bangles','bracelets','necklace','earrings','rings',
	'perfumes','body-sprays'
];

$products = [];
$categoryTitle = 'All Products';

// Build flexible where clause supporting search/category and price range
$conditions = [];
$params = [];
$types = '';

if ($searchParam !== '') {
    $like = '%' . $searchParam . '%';
    $conditions[] = '(name LIKE ? OR category LIKE ? OR subcategory LIKE ?)';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
    $categoryTitle = 'Search Results: "' . e($searchParam) . '"';
} elseif ($categoryParam !== '') {
    if (in_array($categoryParam, $topCategories, true)) {
        $conditions[] = 'category = ?';
        $params[] = $categoryParam;
        $types .= 's';
        $categoryTitle = ucfirst($categoryParam);
    } elseif (in_array($categoryParam, $knownSubcategories, true)) {
        $conditions[] = 'subcategory = ?';
        $params[] = $categoryParam;
        $types .= 's';
        $categoryTitle = ucfirst($categoryParam);
    }
}

// Validate and apply price filters (min_price, max_price)
if ($minPrice !== '') {
    $minVal = floatval($minPrice);
    $conditions[] = 'price >= ?';
    $params[] = $minVal;
    $types .= 'd';
}
if ($maxPrice !== '') {
    $maxVal = floatval($maxPrice);
    $conditions[] = 'price <= ?';
    $params[] = $maxVal;
    $types .= 'd';
}

$whereSql = '';
if (count($conditions) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = 'SELECT id, name, category, subcategory, price, image, description FROM products ' . $whereSql . ' ORDER BY id ASC';
$stmt = $conn->prepare($sql);
if ($stmt) {
    if ($types !== '') {
        // bind params dynamically
        $bind_names = [];
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_name = 'bind' . $i;
            $$bind_name = $params[$i];
            $bind_names[] = &$$bind_name;
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
$conn->close();

$categoryDisplay = [
	'jewelry'   => 'Jewelry',
	'cosmetics' => 'Cosmetics',
	'fragrance' => 'Fragrance'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Jenny's Cosmetics & Jewelry</title>
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
                    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                        <a href="user_account.php" class="top-link"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'My Account'); ?></a>
                    <?php else: ?>
                        <a href="user_login.php" class="top-link"><i class="fas fa-user"></i> My Account</a>
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
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3 mb-4">
                    <div class="filter-sidebar">
                        <h4 class="filter-title">Categories</h4>
                        <ul class="filter-list">
                            <li><a href="products.php" class="filter-link">All Products</a></li>
                            <li><a href="products.php?category=jewelry" class="filter-link">Jewelry</a></li>
                            <li><a href="products.php?category=cosmetics" class="filter-link">Cosmetics</a></li>
                            <li><a href="products.php?category=fragrance" class="filter-link">Fragrance</a></li>
                        </ul>
                        <h4 class="filter-title mt-4">Price Range</h4>
                        <div class="price-filter">
                            <form method="GET" action="products.php" class="row g-2">
                                <div class="col-6">
                                    <input type="number" step="0.01" min="0" name="min_price" class="form-control" placeholder="Min" value="<?php echo htmlspecialchars($minPrice); ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" step="0.01" min="0" name="max_price" class="form-control" placeholder="Max" value="<?php echo htmlspecialchars($maxPrice); ?>">
                                </div>
                                <div class="col-12">
                                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryParam); ?>">
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchParam); ?>">
                                    <button type="submit" class="btn btn-outline-primary w-100 mt-2">Apply Price Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 id="categoryTitle" class="page-title"><?php echo e($categoryTitle); ?></h2>
                    </div>

                    <!-- Dynamic category tabs (for cosmetics and jewelry) -->
                    <?php
                    $cosmeticsSubs = [
                        'complexion' => 'Complexion',
                        'eyes' => 'Eyes',
                        'lips' => 'Lips',
                        'nails' => 'Nails',
                        'skincare' => 'Skin Care',
                        'fragrance' => 'Fragrance'
                    ];
                    $jewelrySubs = [
                        'anklets' => 'Anklets',
                        'bangles' => 'Bangles',
                        'bracelets' => 'Bracelets',
                        'necklace' => 'Necklace',
                        'earrings' => 'Ear Rings',
                        'rings' => 'Rings'
                    ];

                    // determine which main category should be active based on current ?category= param
                    $activeMain = 'cosmetics';
                    if ($categoryParam !== '') {
                        if (in_array($categoryParam, array_keys($cosmeticsSubs), true) || $categoryParam === 'cosmetics') {
                            $activeMain = 'cosmetics';
                        } elseif (in_array($categoryParam, array_keys($jewelrySubs), true) || $categoryParam === 'jewelry') {
                            $activeMain = 'jewelry';
                        }
                    }
                    ?>

                    <!-- Main category tabs -->
                    <ul class="nav nav-tabs mb-2" id="mainCategoryTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?php echo $activeMain === 'cosmetics' ? 'active' : ''; ?>" href="products.php?category=cosmetics">Cosmetics</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?php echo $activeMain === 'jewelry' ? 'active' : ''; ?>" href="products.php?category=jewelry">Jewelry</a>
                        </li>
                    </ul>

                    <!-- Subcategory tabs -->
                    <?php if ($activeMain === 'cosmetics'): ?>
                    <ul class="nav nav-pills mb-4 flex-wrap" id="subCategoryTabs" role="tablist">
                        <?php $first = true; foreach ($cosmeticsSubs as $slug => $label): ?>
                            <li class="nav-item"><a class="nav-link <?php echo ($categoryParam === $slug || ($categoryParam === 'cosmetics' && $first)) ? 'active' : ''; ?>" href="products.php?category=<?php echo $slug; ?>"><?php echo $label; ?></a></li>
                        <?php $first = false; endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <ul class="nav nav-pills mb-4 flex-wrap" id="subCategoryTabs" role="tablist">
                        <?php $first = true; foreach ($jewelrySubs as $slug => $label): ?>
                            <li class="nav-item"><a class="nav-link <?php echo ($categoryParam === $slug || ($categoryParam === 'jewelry' && $first)) ? 'active' : ''; ?>" href="products.php?category=<?php echo $slug; ?>"><?php echo $label; ?></a></li>
                        <?php $first = false; endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <div class="row g-4" id="productsGrid">
                        <?php if (count($products) === 0): ?>
                            <div class="col-12 text-center py-5"><h4>No products found</h4></div>
                        <?php else: ?>
                            <?php foreach ($products as $p): ?>
                                <?php
                                $img = $p['image'];
                                $badge = $categoryDisplay[strtolower($p['category'])] ?? ucfirst($p['category']);
                                ?>
                                <div class="col-md-6 col-lg-4 mb-4 fade-in">
                                    <div class="product-card" data-id="<?php echo (int)$p['id']; ?>" role="button" tabindex="0">
                                        <div class="product-image">
                                            <img src="<?php echo e($img); ?>" alt="<?php echo e($p['name']); ?>" loading="lazy">
                                            <span class="product-badge"><?php echo e($badge); ?></span>
                                        </div>
                                        <div class="product-info">
                                            <div class="product-category"><?php echo e($p['subcategory']); ?></div>
                                            <h3 class="product-name"><?php echo e($p['name']); ?></h3>
                                            <div class="product-price">₨<?php echo e((string)(float)$p['price']); ?></div>
                                            <div class="product-actions">
                                                <?php if ($user_logged_in): ?>
                                                    <form method="POST" action="cart.php" class="d-inline">
                                                        <input type="hidden" name="action" value="add">
                                                        <input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" class="btn-add-cart">
                                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <a href="user_login.php" class="btn-add-cart"><i class="fas fa-sign-in-alt"></i> Login to add</a>
                                                <?php endif; ?>
                                                <a href="product-detail.php?id=<?php echo (int)$p['id']; ?>" class="btn-view">View</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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


