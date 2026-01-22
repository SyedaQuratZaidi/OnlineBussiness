<?php
// Simple database connection test
require_once 'config.php';

try {
    $conn = db();
    echo "<h2>✅ Database Connection Successful!</h2>";

    // Test query
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Total products in database: <strong>" . $row['total'] . "</strong></p>";
        $result->free();
    }

    // Show first few products
    $result = $conn->query("SELECT id, name, category, price FROM products LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "<h3>Sample Products:</h3><ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['name']} - {$row['category']} - ₨{$row['price']}</li>";
        }
        echo "</ul>";
        $result->free();
    }

    $conn->close();
} catch (Exception $e) {
    echo "<h2> Database Connection Failed!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>MySQL service is running in XAMPP</li>";
    echo "<li>Database 'eproject' exists</li>";
    echo "<li>Tables are created with sample data</li>";
    echo "</ul>";
}
?>
