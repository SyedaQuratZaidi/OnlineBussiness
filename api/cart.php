<?php
session_start();
require_once '../config.php';

// Database connection
$conn = db();

header('Content-Type: application/json');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['success' => false, 'message' => '', 'cart_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_REQUEST['action'] ?? '';

    if ($action === 'get_count') {
        $cart_count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['quantity'];
        }
        $response = ['success' => true, 'cart_count' => $cart_count];
    } elseif ($action === 'add') {
        // require user to be logged in to add to cart
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required to add to cart.']);
            $conn->close();
            exit();
        }
        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);

        if ($product_id > 0 && $quantity > 0) {
            // Get product details from database
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();

                // Check if product already in cart
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

                // Calculate cart count
                $cart_count = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $cart_count += $item['quantity'];
                }

                $response = [
                    'success' => true,
                    'message' => 'Product added to cart!',
                    'cart_count' => $cart_count
                ];
            } else {
                $response['message'] = 'Product not found!';
            }
            $stmt->close();
        } else {
            $response['message'] = 'Invalid product or quantity!';
        }
    }
}

echo json_encode($response);
$conn->close();
?>
