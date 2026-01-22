<?php
require_once __DIR__ . '/../config.php';
$conn = db();

$orders = [];
$res = $conn->query("SELECT * FROM orders ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $orders[] = $row;
    }
    $res->free();
}

$productSales = [];
$customersMap = [];
$total_revenue = 0;

foreach ($orders as $ord) {
    $total_revenue += floatval($ord['total']);
    $email = strtolower(trim($ord['customer_email']));
    if (!isset($customersMap[$email])) {
        $customersMap[$email] = [
            'name' => $ord['customer_name'],
            'email' => $ord['customer_email'],
            'orders' => 0,
            'total' => 0
        ];
    }
    $customersMap[$email]['orders'] += 1;
    $customersMap[$email]['total'] += floatval($ord['total']);

    $items = json_decode($ord['order_data'], true);
    if (is_array($items)) {
        foreach ($items as $it) {
            $pid = intval($it['id'] ?? 0);
            if ($pid <= 0) continue;
            $qty = intval($it['quantity'] ?? 0);
            $price = floatval($it['price'] ?? 0);
            if (!isset($productSales[$pid])) {
                $productSales[$pid] = ['id'=>$pid,'name'=>$it['name'] ?? '', 'image'=>$it['image'] ?? '', 'quantity'=>0, 'revenue'=>0];
            }
            $productSales[$pid]['quantity'] += $qty;
            $productSales[$pid]['revenue'] += $qty * $price;
        }
    }
}

usort($top_products = array_values($productSales), function($a,$b){ return $b['quantity'] <=> $a['quantity'];});
$top_products = array_slice($top_products, 0, 10);

usort($top_customers = array_values($customersMap), function($a,$b){ return $b['total'] <=> $a['total'];});
$top_customers = array_slice($top_customers, 0, 10);

echo "Top Products:\n";
foreach ($top_products as $i => $p) {
    printf("%d. %s — Qty: %d — Revenue: %s\n", $i+1, $p['name'], $p['quantity'], number_format($p['revenue'], 2));
}

echo "\nTop Customers:\n";
foreach ($top_customers as $i => $c) {
    printf("%d. %s <%s> — Orders: %d — Total: %s\n", $i+1, $c['name'], $c['email'], $c['orders'], number_format($c['total'], 2));
}

$conn->close();
