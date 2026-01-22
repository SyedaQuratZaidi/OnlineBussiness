<?php
session_start();

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../config.php';

// Database connection
$conn = db();

// Handle product operations
$message = '';
$message_type = '';

// File upload function
function uploadImage($file, $currentImage = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return $currentImage; // Return current image if no new file uploaded
    }

    $uploadDir = '../assets/images/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }

    // Validate file size
    if ($file['size'] > $maxSize) {
        return false;
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('product_') . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Delete old image if it exists and is not a default/system image
        if ($currentImage && file_exists('../' . $currentImage) && strpos($currentImage, 'assets/images/') === 0) {
            $oldFile = '../' . $currentImage;
            if (file_exists($oldFile) && !in_array(basename($oldFile), ['Online Jewellery & Cosmetic Store.jpg', 'jewlarry.jpg', 'cosmetic.webp', 'fragnace.jpg'])) {
                unlink($oldFile);
            }
        }
        return 'assets/images/' . $filename;
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $subcategory = trim($_POST['subcategory'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Handle image upload
            $image = uploadImage($_FILES['image'] ?? null);

            if ($name && $price > 0 && $category && $subcategory && $image) {
                $stmt = $conn->prepare("INSERT INTO products (name, category, subcategory, price, image, description) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssdss", $name, $category, $subcategory, $price, $image, $description);
                if ($stmt->execute()) {
                    $message = 'Product added successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error adding product: ' . $conn->error;
                    $message_type = 'error';
                }
                $stmt->close();
            } else {
                if ($image === false) {
                    $message = 'Invalid image file. Please upload a valid image (JPG, PNG, GIF, WebP) under 5MB.';
                } else {
                    $message = 'Please fill all required fields!';
                }
                $message_type = 'error';
            }
        } elseif ($action === 'edit') {
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $subcategory = trim($_POST['subcategory'] ?? '');
            $currentImage = trim($_POST['current_image'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Handle image upload (optional for edit)
            $image = uploadImage($_FILES['image'] ?? null, $currentImage);
            if ($image === false) {
                $message = 'Invalid image file. Please upload a valid image (JPG, PNG, GIF, WebP) under 5MB.';
                $message_type = 'error';
            } elseif ($id > 0 && $name && $price > 0 && $category && $subcategory && $image) {
                $stmt = $conn->prepare("UPDATE products SET name=?, category=?, subcategory=?, price=?, image=?, description=? WHERE id=?");
                $stmt->bind_param("sssdssi", $name, $category, $subcategory, $price, $image, $description, $id);
                if ($stmt->execute()) {
                    $message = 'Product updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating product: ' . $conn->error;
                    $message_type = 'error';
                }
                $stmt->close();
            } else {
                $message = 'Please fill all required fields!';
                $message_type = 'error';
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $message = 'Product deleted successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error deleting product: ' . $conn->error;
                    $message_type = 'error';
                }
                $stmt->close();
            }
        } elseif ($action === 'update_order_status') {
            $order_id = intval($_POST['order_id'] ?? 0);
            $new_status = trim($_POST['status'] ?? '');
            $allowed = ['pending','processing','shipped','delivered','cancelled'];
            if ($order_id > 0 && in_array($new_status, $allowed, true)) {
                $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
                $stmt->bind_param('si', $new_status, $order_id);
                if ($stmt->execute()) {
                    $message = 'Order status updated successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to update order status: ' . $conn->error;
                    $message_type = 'error';
                }
                $stmt->close();
            }
        } elseif ($action === 'add_category') {
            $name = trim($_POST['category_name'] ?? '');
            $slug = trim($_POST['category_slug'] ?? '');
            if ($name) {
                if ($slug === '') {
                    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
                }
                $stmt = $conn->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
                $stmt->bind_param('ss', $name, $slug);
                if ($stmt->execute()) {
                    $message = 'Category added successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to add category: ' . $conn->error;
                    $message_type = 'error';
                }
                $stmt->close();
            }
        } elseif ($action === 'edit_category') {
            $cid = intval($_POST['category_id'] ?? 0);
            $name = trim($_POST['category_name'] ?? '');
            $slug = trim($_POST['category_slug'] ?? '');
            if ($cid > 0 && $name) {
                if ($slug === '') {
                    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
                }
                $stmt = $conn->prepare('UPDATE categories SET name = ?, slug = ? WHERE id = ?');
                $stmt->bind_param('ssi', $name, $slug, $cid);
                if ($stmt->execute()) {
                    $message = 'Category updated successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to update category: ' . $conn->error;
                    $message_type = 'error';
                }
                $stmt->close();
            }
        } elseif ($action === 'delete_category') {
            $cid = intval($_POST['category_id'] ?? 0);
            if ($cid > 0) {
                // Optionally, reassign or prevent deletion if products exist. For now, allow delete.
                $stmt = $conn->prepare('DELETE FROM categories WHERE id = ?');
                $stmt->bind_param('i', $cid);
                if ($stmt->execute()) {
                    $message = 'Category deleted.';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to delete category: ' . $conn->error;
                    $message_type = 'error';
                }
                $stmt->close();
            }
        }
        // end update_order_status
    } // end if isset(action)
} // end if POST

// Get products
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $result->free();
}

// Get stats
$total_products = count($products);
// total orders
$res = $conn->query('SELECT COUNT(*) AS c, COALESCE(SUM(total),0) AS revenue FROM orders');
$row = $res ? $res->fetch_assoc() : null;
$total_orders = $row ? (int)$row['c'] : 0;
$total_revenue = $row ? (float)$row['revenue'] : 0.0;
if ($res) $res->free();
// total customers (distinct emails)
$res2 = $conn->query('SELECT COUNT(DISTINCT customer_email) AS c FROM orders');
$row2 = $res2 ? $res2->fetch_assoc() : null;
$total_customers = $row2 ? (int)$row2['c'] : 0;
if ($res2) $res2->free();

// Recent orders (latest 5)
$recent_orders = [];
$stmt = $conn->prepare('SELECT id, customer_name, customer_email, total, status, created_at FROM orders ORDER BY created_at DESC LIMIT 5');
if ($stmt) {
    $stmt->execute();
    $res3 = $stmt->get_result();
    while ($r = $res3->fetch_assoc()) {
        $recent_orders[] = $r;
    }
    $stmt->close();

    // Ensure categories table exists; create if missing
    $tableCheck = $conn->query("SHOW TABLES LIKE 'categories'");
    if ($tableCheck && $tableCheck->num_rows === 0) {
        $createSql = "CREATE TABLE IF NOT EXISTS `categories` (
          `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `slug` varchar(255) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        if ($conn->query($createSql) === TRUE) {
            $message = 'Categories table was missing and has been created.';
            $message_type = 'success';
        } else {
            $message = 'Categories table missing and could not be created: ' . $conn->error;
            $message_type = 'error';
        }
    }

    // load categories for management (safe if table exists)
    $categories = [];
    $cres = $conn->query("SELECT * FROM categories ORDER BY name ASC");
    if ($cres) {
        while ($crow = $cres->fetch_assoc()) {
            $categories[] = $crow;
        }
        $cres->free();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Jenny's Cosmetics & Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css?v=2">
</head>
<body>
    <button id="sidebarEdgeToggle" class="sidebar-edge-toggle" aria-label="Open sidebar"></button>
    <button id="sidebarHandle" class="sidebar-handle" aria-label="Open sidebar"></button>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">  
        <div class="sidebar-header">
            <h3><i class="fas fa-gem"></i> Jenny's Admin</h3>
        </div>
        <nav class="sidebar-nav">
            <a href="#dashboard" class="nav-item active" data-page="dashboard">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="#products" class="nav-item" data-page="products">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="#categories" class="nav-item" data-page="categories">
                <i class="fas fa-tags"></i> Categories
            </a>
            <a href="#orders" class="nav-item" data-page="orders">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="#reports" class="nav-item" data-page="reports">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a href="#backup" class="nav-item" data-page="backup">
                <i class="fas fa-database"></i> Backup
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar-admin">
            <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-white me-3"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Dashboard Page -->
        <div class="page-content" id="dashboardPage">
            <div class="page-header">
                <h2>Dashboard</h2>
                <p class="text-muted">Welcome to Admin Dashboard</p>
            </div>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?php echo $total_products; ?></h3>
                            <p>Total Products</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?php echo $total_orders; ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?php echo $total_customers; ?></h3>
                            <p>Total Customers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stats-content">
                            <h3>₨<?php echo number_format($total_revenue); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_orders)): ?>
                                        <tr><td colspan="6" class="text-center">No orders yet</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_orders as $o): ?>
                                            <tr>
                                                <td><?php echo (int)$o['id']; ?></td>
                                                <td><?php echo htmlspecialchars($o['customer_name'] ?: $o['customer_email']); ?><br><small class="text-muted"><?php echo htmlspecialchars($o['customer_email']); ?></small></td>
                                                <td><?php echo htmlspecialchars($o['created_at']); ?></td>
                                                <td>₨<?php echo number_format((float)$o['total'], 2); ?></td>
                                                <td><span class="badge bg-<?php echo ($o['status']==='delivered' ? 'success' : ($o['status']==='processing' ? 'warning' : ($o['status']==='shipped' ? 'info' : ($o['status']==='cancelled' ? 'danger' : 'secondary')))); ?>"><?php echo htmlspecialchars(ucfirst($o['status'])); ?></span></td>
                                                <td>
                                                    <form method="POST" class="d-flex gap-2">
                                                        <input type="hidden" name="action" value="update_order_status">
                                                        <input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>">
                                                        <select name="status" class="form-select form-select-sm">
                                                            <?php $opts = ['pending','processing','shipped','delivered','cancelled']; foreach ($opts as $st): ?>
                                                                <option value="<?php echo $st; ?>" <?php if ($o['status']===$st) echo 'selected'; ?>><?php echo ucfirst($st); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Page -->
        <div class="page-content" id="productsPage" style="display: none;">
            <div class="page-header d-flex align-items-center">
                <div>
                    <h2>Products Management</h2>
                    <p class="text-muted">Manage your products</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
            </div>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="productsTable">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;"></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($product['category']); ?></span></td>
                                    <td>₨<?php echo number_format($product['price']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['category']); ?>', '<?php echo htmlspecialchars($product['subcategory']); ?>')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No products found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Page -->
        <div class="page-content" id="categoriesPage" style="display: none;">
            <div class="page-header d-flex align-items-center">
                <div>
                    <h2>Categories Management</h2>
                    <p class="text-muted">Create, edit, and delete categories</p>
                </div>
                <div class="header-actions ms-auto">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </div>
            </div>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr><td colspan="5" class="text-center">No categories defined yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td><?php echo (int)$cat['id']; ?></td>
                                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                            <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                                            <td><?php echo htmlspecialchars($cat['created_at'] ?? ''); ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-primary" onclick="editCategory(<?php echo (int)$cat['id']; ?>, '<?php echo htmlspecialchars(addslashes($cat['name'])); ?>', '<?php echo htmlspecialchars(addslashes($cat['slug'])); ?>')"><i class="fas fa-edit"></i> Edit</button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteCategory(<?php echo (int)$cat['id']; ?>)"><i class="fas fa-trash"></i> Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add_category">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="category_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug (optional)</label>
                                <input type="text" name="category_slug" class="form-control" placeholder="auto-generated if empty">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div class="modal fade" id="editCategoryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="edit_category">
                        <input type="hidden" name="category_id" id="editCategoryId">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="category_name" id="editCategoryName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug (optional)</label>
                                <input type="text" name="category_slug" id="editCategorySlug" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Orders Page -->
        <div class="page-content" id="ordersPage" style="display: none;">
            <div class="page-header">
                <h2>Orders Management</h2>
                <p class="text-muted">View and manage orders</p>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <form method="GET" class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label class="form-label mb-0">Filter by status</label>
                            </div>
                            <div class="col-auto">
                                <select name="filter_status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="pending" <?php if(isset($_GET['filter_status']) && $_GET['filter_status']==='pending') echo 'selected'; ?>>Pending</option>
                                    <option value="processing" <?php if(isset($_GET['filter_status']) && $_GET['filter_status']==='processing') echo 'selected'; ?>>Processing</option>
                                    <option value="shipped" <?php if(isset($_GET['filter_status']) && $_GET['filter_status']==='shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="delivered" <?php if(isset($_GET['filter_status']) && $_GET['filter_status']==='delivered') echo 'selected'; ?>>Delivered</option>
                                    <option value="cancelled" <?php if(isset($_GET['filter_status']) && $_GET['filter_status']==='cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-secondary" type="submit">Apply</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // prepare orders query with optional status filter
                                $filter_status = trim($_GET['filter_status'] ?? '');
                                $allowed_statuses = ['', 'pending','processing','shipped','delivered','cancelled'];
                                if (!in_array($filter_status, $allowed_statuses, true)) $filter_status = '';
                                if ($filter_status !== '') {
                                    $stmt = $conn->prepare('SELECT id, customer_name, customer_email, order_data, total, status, created_at FROM orders WHERE status = ? ORDER BY created_at DESC');
                                    $stmt->bind_param('s', $filter_status);
                                } else {
                                    $stmt = $conn->prepare('SELECT id, customer_name, customer_email, order_data, total, status, created_at FROM orders ORDER BY created_at DESC');
                                }
                                if ($stmt) {
                                    $stmt->execute();
                                    $resAll = $stmt->get_result();
                                    while ($ord = $resAll->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo (int)$ord['id']; ?></td>
                                    <td><?php echo htmlspecialchars($ord['customer_name'] ?: $ord['customer_email']); ?><br><small class="text-muted"><?php echo htmlspecialchars($ord['customer_email']); ?></small></td>
                                    <td><?php echo htmlspecialchars($ord['created_at']); ?></td>
                                    <td>
                                        <?php $items = json_decode($ord['order_data'], true); if (is_array($items)): ?>
                                            <ul class="mb-0">
                                                <?php foreach ($items as $it): ?>
                                                    <li><?php echo htmlspecialchars($it['name'] ?? 'Item'); ?> × <?php echo (int)($it['quantity'] ?? 1); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <em>Not available</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>₨<?php echo number_format((float)$ord['total'], 2); ?></td>
                                    <td><span class="badge bg-<?php echo ($ord['status']==='delivered' ? 'success' : ($ord['status']==='processing' ? 'warning' : ($ord['status']==='shipped' ? 'info' : ($ord['status']==='cancelled' ? 'danger' : 'secondary')))); ?>"><?php echo htmlspecialchars(ucfirst($ord['status'])); ?></span></td>
                                    <td>
                                        <form method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="action" value="update_order_status">
                                            <input type="hidden" name="order_id" value="<?php echo (int)$ord['id']; ?>">
                                            <select name="status" class="form-select form-select-sm">
                                                <?php $opts = ['pending','processing','shipped','delivered','cancelled']; foreach ($opts as $st): ?>
                                                    <option value="<?php echo $st; ?>" <?php if ($ord['status']===$st) echo 'selected'; ?>><?php echo ucfirst($st); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                                    endwhile;
                                    $stmt->close();
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Page -->
        <div class="page-content" id="reportsPage" style="display: none;">
            <div class="page-header">
                <h2>Reports</h2>
                <p class="text-muted">View sales reports</p>
            </div>
            <div class="card">
                <div class="card-body">
                    <p>Generate reports of top products and customers.</p>
                    <div class="d-flex gap-2">
                        <a href="reports.php?download=top10" class="btn btn-primary" target="_blank">Download Top 10 Products & Customers (CSV)</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup Page -->
        <div class="page-content" id="backupPage" style="display: none;">
            <div class="page-header">
                <h2>Database Backup</h2>
                <p class="text-muted">Backup and restore your data</p>
            </div>
            <div class="card">
                <div class="card-body">
                    <p>You can download a full SQL dump of the `<?php echo e(DB_NAME); ?>` database. The dump uses the server's <code>mysqldump</code> utility when available.</p>
                    <div class="d-flex gap-2">
                        <a href="backup.php?download=1" class="btn btn-primary">Download Database Backup (SQL)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productName" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="productName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productPrice" class="form-label">Price *</label>
                                <input type="number" class="form-control" id="productPrice" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productCategory" class="form-label">Category *</label>
                                <select class="form-control" id="productCategory" name="category" required onchange="updateSubcategories('productSubcategory')">
                                    <option value="">Select Category</option>
                                    <option value="cosmetics">Cosmetics</option>
                                    <option value="jewelry">Jewelry</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productSubcategory" class="form-label">Subcategory *</label>
                                <select class="form-control" id="productSubcategory" name="subcategory" required>
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="productImage" class="form-label">Product Image *</label>
                            <input type="file" class="form-control" id="productImage" name="image" accept="image/*" required>
                            <div class="form-text">Accepted formats: JPG, PNG, GIF, WebP</div>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editProductId">
                    <input type="hidden" name="current_image" id="editCurrentImage">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editProductName" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="editProductName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editProductPrice" class="form-label">Price *</label>
                                <input type="number" class="form-control" id="editProductPrice" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editProductCategory" class="form-label">Category *</label>
                                <select class="form-control" id="editProductCategory" name="category" required onchange="updateSubcategories('editProductSubcategory')">
                                    <option value="">Select Category</option>
                                    <option value="cosmetics">Cosmetics</option>
                                    <option value="jewelry">Jewelry</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editProductSubcategory" class="form-label">Subcategory *</label>
                                <select class="form-control" id="editProductSubcategory" name="subcategory" required>
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editProductImage" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="editProductImage" name="image" accept="image/*">
                            <div class="form-text">Leave empty to keep current image. Accepted formats: JPG, PNG, GIF, WebP</div>
                            <div id="currentImagePreview" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label for="editProductDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editProductDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navigation
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item[data-page]');
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.getAttribute('data-page');
                    showPage(page);

                    navItems.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Initialize category change listeners
            document.getElementById('productCategory').addEventListener('change', function() {
                updateSubcategories('productSubcategory');
            });
            document.getElementById('editProductCategory').addEventListener('change', function() {
                updateSubcategories('editProductSubcategory');
            });

            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });

        function showPage(pageName) {
            const pages = document.querySelectorAll('.page-content');
            pages.forEach(page => {
                page.style.display = 'none';
            });

            const targetPage = document.getElementById(pageName + 'Page');
            if (targetPage) {
                targetPage.style.display = 'block';
            }
        }

        // Build categories map from server-side products (category -> [subcategories])
        <?php
            $categories_map = [];
            foreach ($products as $p) {
                $cat = isset($p['category']) ? trim(strtolower($p['category'])) : '';
                $sub = isset($p['subcategory']) ? trim(strtolower($p['subcategory'])) : '';
                if ($cat !== '') {
                    if (!isset($categories_map[$cat])) $categories_map[$cat] = [];
                    if ($sub !== '' && !in_array($sub, $categories_map[$cat], true)) {
                        $categories_map[$cat][] = $sub;
                    }
                }
            }
        ?>

        let subcategoryOptions = <?php echo json_encode($categories_map); ?>;

        // Desired defaults and order (ensure these are always available)
        const categoryDefaults = {
            'cosmetics': ['complexion','eyes','lips','nails','skincare','fragrance'],
            'jewelry': ['anklets','bangles','bracelets','necklace','earrings','rings'],
            'fragrance': ['body-sprays','perfumes']
        };
        const categoriesOrder = ['cosmetics', 'jewelry', 'fragrance'];

        // Merge defaults with existing data (preserve default order and append any additional existing subcategories)
        categoriesOrder.forEach(cat => {
            const existing = subcategoryOptions[cat] || [];
            const merged = [];
            (categoryDefaults[cat] || []).forEach(s => { if (!merged.includes(s)) merged.push(s); });
            existing.forEach(s => { if (!merged.includes(s)) merged.push(s); });
            subcategoryOptions[cat] = merged;
        });

        // Ensure any other categories from the data remain present
        Object.keys(subcategoryOptions).forEach(cat => {
            if (!categoriesOrder.includes(cat)) {
                // keep as-is
            }
        });

        // Display name overrides for certain subcategories
        const displayMap = {
            'skincare': 'Skin Care',
            'earrings': 'Ear Rings',
            'body-sprays': 'Body Sprays'
        };

        function formatLabel(key) {
            if (displayMap[key]) return displayMap[key];
            return key.replace(/-/g, ' ').split(' ').map(p => p.charAt(0).toUpperCase() + p.slice(1)).join(' ');
        }

        function populateCategorySelects() {
            const prodCat = document.getElementById('productCategory');
            const editCat = document.getElementById('editProductCategory');
            if (!prodCat || !editCat) return;

            // Clear existing options but keep placeholder
            prodCat.innerHTML = '<option value="">Select Category</option>';
            editCat.innerHTML = '<option value="">Select Category</option>';

            // Add categories in desired order first
            categoriesOrder.forEach(cat => {
                if (subcategoryOptions[cat]) {
                    const opt = document.createElement('option');
                    opt.value = cat;
                    opt.textContent = cat.charAt(0).toUpperCase() + cat.slice(1);
                    prodCat.appendChild(opt);
                    editCat.appendChild(opt.cloneNode(true));
                }
            });

            // Add any remaining categories
            Object.keys(subcategoryOptions).forEach(cat => {
                if (!categoriesOrder.includes(cat)) {
                    const opt = document.createElement('option');
                    opt.value = cat;
                    opt.textContent = cat.charAt(0).toUpperCase() + cat.slice(1);
                    prodCat.appendChild(opt);
                    editCat.appendChild(opt.cloneNode(true));
                }
            });
        }

        function updateSubcategories(selectId) {
            const categorySelect = document.getElementById(selectId === 'editProductSubcategory' ? 'editProductCategory' : 'productCategory');
            const subcategorySelect = document.getElementById(selectId);

            const selectedCategory = categorySelect.value;
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';

            if (selectedCategory && subcategoryOptions[selectedCategory]) {
                subcategoryOptions[selectedCategory].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub;
                    option.textContent = formatLabel(sub);
                    subcategorySelect.appendChild(option);
                });
            }
        }

        // Initialize category dropdowns on load
        document.addEventListener('DOMContentLoaded', function() {
            populateCategorySelects();
            // If there is a pre-set value (e.g., when editing), ensure subcategories are initialized
            updateSubcategories('productSubcategory');
            updateSubcategories('editProductSubcategory');

            // Also populate selects when Add/Edit modals are shown (ensures up-to-date lists)
            const addModal = document.getElementById('addProductModal');
            if (addModal) {
                addModal.addEventListener('show.bs.modal', function() {
                    populateCategorySelects();
                    updateSubcategories('productSubcategory');
                });
            }

            const editModal = document.getElementById('editProductModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function() {
                    populateCategorySelects();
                    updateSubcategories('editProductSubcategory');
                });
            }
        });

        function editProduct(id, category, subcategory) {
            // Find product data and populate edit modal
            const rows = document.querySelectorAll('#productsTable tbody tr');
            rows.forEach(row => {
                const buttons = row.querySelectorAll('button');
                buttons.forEach(button => {
                    if (button.onclick && button.onclick.toString().includes(id)) {
                        const cells = row.querySelectorAll('td');
                        if (cells.length >= 4) {
                            const imgSrc = cells[0].querySelector('img').src;
                            const imagePath = imgSrc.replace(window.location.origin + '/', '');

                            document.getElementById('editProductId').value = id;
                            document.getElementById('editCurrentImage').value = imagePath;
                            document.getElementById('editProductName').value = cells[1].textContent.trim();
                            document.getElementById('editProductPrice').value = cells[3].textContent.replace('₨', '').replace(/,/g, '');

                            document.getElementById('editProductCategory').value = category;

                            // Update subcategories dropdown
                            updateSubcategories('editProductSubcategory');

                            // Set subcategory value after options are populated
                            setTimeout(() => {
                                document.getElementById('editProductSubcategory').value = subcategory;
                            }, 100);

                            // Show current image preview
                            const previewDiv = document.getElementById('currentImagePreview');
                            previewDiv.innerHTML = `<small class="text-muted">Current image:</small><br><img src="${imgSrc}" style="max-width: 100px; max-height: 100px;" class="mt-1">`;

                            document.getElementById('editProductDescription').value = '';

                            new bootstrap.Modal(document.getElementById('editProductModal')).show();
                        }
                    }
                });
            });
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                form.appendChild(actionInput);

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                form.appendChild(idInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Category helpers
        function editCategory(id, name, slug) {
            document.getElementById('editCategoryId').value = id;
            document.getElementById('editCategoryName').value = name;
            document.getElementById('editCategorySlug').value = slug;
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        }

        function deleteCategory(id) {
            if (!confirm('Delete this category? This cannot be undone.')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete_category';
            form.appendChild(actionInput);

            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'category_id';
            idInput.value = id;
            form.appendChild(idInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
