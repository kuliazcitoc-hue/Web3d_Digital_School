<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require "db.php";

$method = $_SERVER["REQUEST_METHOD"];

// GET historial
if ($method === "GET") {
    $userId = intval($_GET["userId"] ?? 0);
    if (!$userId) { echo json_encode(["error" => "Falta userId"]); exit; }

    $stmt = $conn->prepare("SELECT pr.id, pr.product_id, pr.buyer_id, pr.seller_id, pr.quantity, pr.total, pr.created_at, p.nombre AS producto_nombre, ub.nombre AS comprador_nombre, us.nombre AS vendedor_nombre FROM purchases pr JOIN products p ON p.id = pr.product_id JOIN users ub ON ub.id = pr.buyer_id JOIN users us ON us.id = pr.seller_id WHERE pr.buyer_id = ? OR pr.seller_id = ? ORDER BY pr.created_at DESC LIMIT 50");
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    $rows = $stmt->get_result();
    $purchases = [];
    while ($r = $rows->fetch_assoc()) $purchases[] = $r;
    echo json_encode(["purchases" => $purchases]); exit;
}

// POST comprar
if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $buyerId = intval($data["buyerId"] ?? 0);
    $items   = $data["cart"] ?? null;
    $productId = intval($data["productId"] ?? 0);
    $qty       = intval($data["quantity"] ?? 1);

    if (!$buyerId) { echo json_encode(["error" => "Datos inválidos"]); exit; }

    $cartItems = [];
    if (is_array($items) && !empty($items)) {
        foreach ($items as $item) {
            if (!isset($item["productId"], $item["quantity"])) continue;
            $pid = intval($item["productId"]);
            $q   = intval($item["quantity"]);
            if ($pid > 0 && $q > 0) $cartItems[] = ["productId" => $pid, "quantity" => $q];
        }
    } elseif ($productId > 0 && $qty > 0) {
        $cartItems[] = ["productId" => $productId, "quantity" => $qty];
    }

    if (empty($cartItems)) { echo json_encode(["error" => "Carrito vacío o datos inválidos"]); exit; }

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $buyerId); $stmt->execute();
    $buyer = $stmt->get_result()->fetch_assoc();
    if (!$buyer) { http_response_code(404); echo json_encode(["error" => "Comprador no encontrado"]); exit; }

    $totalOrder = 0;
    $productsToProcess = [];

    foreach ($cartItems as $entry) {
        $pid = $entry["productId"];
        $q = $entry["quantity"];

        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1 AND deleted_at IS NULL");
        $stmt->bind_param("i", $pid); $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        if (!$product) { echo json_encode(["error" => "Producto no encontrado: " . $pid]); exit; }
        if ($product["stock"] < $q) { echo json_encode(["error" => "Stock insuficiente para: " . $product["nombre"]]); exit; }
        if ($product["seller_id"] == $buyerId) { echo json_encode(["error" => "No puedes comprar tu propio producto: " . $product["nombre"]]); exit; }

        $productsToProcess[] = [
            "product" => $product,
            "qty" => $q,
            "lineTotal" => intval($product["precio"]) * $q
        ];
        $totalOrder += intval($product["precio"]) * $q;
    }

    if ($buyer["monedas"] < $totalOrder) { echo json_encode(["error" => "Monedas insuficientes para este carrito"]); exit; }

    $conn->begin_transaction();
    try {
        foreach ($productsToProcess as $entry) {
            $product = $entry["product"];
            $qty = $entry["qty"];
            $total = $entry["lineTotal"];
            $sellerId = intval($product["seller_id"]);
            $newStock = $product["stock"] - $qty;

            $stmt = $conn->prepare("UPDATE products SET stock = ?, is_active = ? WHERE id = ? AND stock >= ? AND is_active = 1 AND deleted_at IS NULL");
            $active = $newStock > 0 ? 1 : 0;
            $stmt->bind_param("iiii", $newStock, $active, $product["id"], $qty);
            $stmt->execute();
            if ($stmt->affected_rows !== 1) {
                throw new RuntimeException("Stock insuficiente para completar la compra");
            }

            $stmt = $conn->prepare("UPDATE users SET monedas = monedas - ? WHERE id = ?");
            $stmt->bind_param("ii", $total, $buyerId); $stmt->execute();

            $gain = $total - 1;
            $stmt = $conn->prepare("UPDATE users SET monedas = monedas + ? WHERE id = ?");
            $stmt->bind_param("ii", $gain, $sellerId); $stmt->execute();

            $conn->query("UPDATE admin SET total_comisiones = total_comisiones + 1 WHERE id = 1");

            $now = date("Y-m-d H:i:s");
            $stmt = $conn->prepare("INSERT INTO purchases (product_id, buyer_id, seller_id, quantity, total, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiis", $product["id"], $buyerId, $sellerId, $qty, $total, $now);
            $stmt->execute();

            $stmt = $conn->prepare("SELECT id FROM conversations WHERE product_id = ? AND buyer_id = ? AND seller_id = ?");
            $stmt->bind_param("iii", $product["id"], $buyerId, $sellerId);
            $stmt->execute();
            $conversation = $stmt->get_result()->fetch_assoc();

            if (!$conversation) {
                $conversationNow = date("Y-m-d H:i:s");
                $stmt = $conn->prepare("INSERT INTO conversations (product_id, buyer_id, seller_id, created_at, updated_at, status) VALUES (?, ?, ?, ?, ?, 'active')");
                $stmt->bind_param("iiiss", $product["id"], $buyerId, $sellerId, $conversationNow, $conversationNow);
                $stmt->execute();
            }
        }

        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollback();
        echo json_encode(["error" => "No se pudo completar la compra"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, nombre, curso, rol, contacto, monedas, card_code, is_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $buyerId); $stmt->execute();
    $buyerUpdated = $stmt->get_result()->fetch_assoc();

    echo json_encode(["ok" => true, "buyer" => $buyerUpdated, "total" => $totalOrder]); exit;
}
