<?php

// Temporary: product count endpoint — remove after use
header('Content-Type: application/json');

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=u423848738_econtainer_db;charset=utf8mb4',
        'u423848738_econtainer',
        'Econtainer20'
    );

    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total, 
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active, 
            SUM(CASE WHEN status != 'active' THEN 1 ELSE 0 END) as inactive 
        FROM products
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
