<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
require_once '..//config.php';
$conn = db();

if (isset($_GET['download']) && $_GET['download'] === 'top10') {
    // prepare top products by quantity and revenue
    $product_stats = []; // key by product id or name
    $stmt = $conn->prepare('SELECT order_data FROM orders');
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $items = json_decode($row['order_data'], true);
            if (is_array($items)) {
                foreach ($items as $it) {
                    $pid = isset($it['id']) ? (string)$it['id'] : (string)($it['name'] ?? '');
                    $pname = $it['name'] ?? '';
                    $qty = (int)($it['quantity'] ?? 0);
                    $price = (float)($it['price'] ?? 0);
                    if (!isset($product_stats[$pid])) {
                        $product_stats[$pid] = ['id' => $pid, 'name' => $pname, 'qty' => 0, 'revenue' => 0.0];
                    }
                    $product_stats[$pid]['qty'] += $qty;
                    $product_stats[$pid]['revenue'] += $qty * $price;
                }
            }
        }
        $stmt->close();
    }

    // sort products by qty desc
    usort($product_stats, function($a, $b) {
        return $b['qty'] <=> $a['qty'];
    });

    // top 10
    $top_products = array_slice($product_stats, 0, 10);

    // top customers by total spent
    $top_customers = [];
    $res2 = $conn->query("SELECT customer_email, customer_name, COALESCE(SUM(total),0) AS total_spent, COUNT(*) AS orders_count FROM orders GROUP BY customer_email ORDER BY total_spent DESC LIMIT 10");
    if ($res2) {
        while ($r = $res2->fetch_assoc()) {
            $top_customers[] = $r;
        }
        $res2->free();
    }

    // output CSV
    $filename = 'top10_report_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $out = fopen('php://output', 'w');

    // products section
    fputcsv($out, ['Top 10 Products']);
    fputcsv($out, ['Product ID', 'Product Name', 'Total Quantity Sold', 'Total Revenue']);
    foreach ($top_products as $p) {
        fputcsv($out, [$p['id'], $p['name'], $p['qty'], number_format($p['revenue'], 2)]);
    }
    fputcsv($out, []);

    // customers section
    fputcsv($out, ['Top 10 Customers']);
    fputcsv($out, ['Customer Email', 'Customer Name', 'Orders Count', 'Total Spent']);
    foreach ($top_customers as $c) {
        fputcsv($out, [$c['customer_email'], $c['customer_name'], (int)$c['orders_count'], number_format((float)$c['total_spent'], 2)]);
    }

    fclose($out);
    $conn->close();
    exit();
}

// if not download action, show a small page with the link
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reports - Admin</title>
</head>
<body>
    <h2>Reports</h2>
    <p><a href="reports.php?download=top10">Download Top 10 Products & Customers (CSV)</a></p>
</body>
</html>
