<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Ensure $conn is available. Some configs may expose the PDO instance under a different name.
if (!isset($conn)) {
    if (isset($pdo)) {
        $conn = $pdo;
    } elseif (isset($dbh)) {
        $conn = $dbh;
    } elseif (isset($db)) {
        $conn = $db;
    } else {
        // No connection available; stop with an error.
        die('Database connection not found.');
    }
}

$product_id = $_GET['id'] ?? null;

if ($product_id) {

        // Helper to execute delete for PDO, mysqli, or compatible connection objects
        function executeDelete(object $conn, string $sql, int|string $id): bool
        {
            // Normalize id to integer
            $id = (int)$id;
            if ($conn instanceof PDO) {
                $stmt = $conn->prepare($sql);
                return $stmt->execute([':id' => $id]);
            } elseif ($conn instanceof mysqli) {
                $stmt = $conn->prepare($sql);
                if (!$stmt) return false;
                $stmt->bind_param('i', $id);
                return $stmt->execute();
            } elseif (method_exists($conn, 'prepare')) {
                $stmt = $conn->prepare($sql);
                return $stmt ? $stmt->execute([':id' => $id]) : false;
            } else {
                // Unknown connection type
                return false;
            }
        }

        // Remove from cart
        executeDelete($conn, "DELETE FROM cart_items WHERE product_id = :id", $product_id);

        // Remove from order items
        executeDelete($conn, "DELETE FROM order_items WHERE product_id = :id", $product_id);

        // Delete product
        executeDelete($conn, "DELETE FROM products WHERE id = :id", $product_id);
}

header("Location: manage_products.php");
exit();
?>