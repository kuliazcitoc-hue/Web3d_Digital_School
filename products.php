<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require "db.php";

$method = $_SERVER["REQUEST_METHOD"];
$id     = intval($_GET["id"] ?? 0);
$action = $_GET["action"] ?? "";

// GET /api/products.php?popular=1
if ($method === "GET" && isset($_GET["popular"])) {
    $rows = $conn->query("SELECT p.*, u.nombre AS vendedor_nombre, u.contacto AS vendedor_contacto, u.curso AS vendedor_curso FROM products p JOIN users u ON u.id = p.seller_id WHERE p.promoted_until IS NOT NULL AND p.promoted_until >= NOW() AND p.is_active = 1 AND p.stock > 0 AND p.deleted_at IS NULL ORDER BY p.promoted_until DESC");
    $products = [];
    while ($r = $rows->fetch_assoc()) $products[] = $r;
    echo json_encode(["products" => $products]); exit;
}

// GET /api/products.php?sellerId=... — productos propios, incluidos los agotados
if ($method === "GET" && isset($_GET["sellerId"])) {
    $sellerId = intval($_GET["sellerId"]);
    $stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ? AND deleted_at IS NULL ORDER BY created_at DESC");
    $stmt->bind_param("i", $sellerId);
    $stmt->execute();
    $rows = $stmt->get_result();
    $products = [];
    while ($r = $rows->fetch_assoc()) $products[] = $r;
    echo json_encode(["products" => $products]); exit;
}

// GET /api/products.php
if ($method === "GET" && !$id) {
    $rows = $conn->query("SELECT p.*, u.nombre AS vendedor_nombre, u.contacto AS vendedor_contacto, u.curso AS vendedor_curso FROM products p JOIN users u ON u.id = p.seller_id WHERE p.is_active = 1 AND p.stock > 0 AND p.deleted_at IS NULL ORDER BY p.created_at DESC");
    $products = [];
    while ($r = $rows->fetch_assoc()) $products[] = $r;
    echo json_encode(["products" => $products]); exit;
}

// POST crear producto
if ($method === "POST" && !$id) {
    $data       = json_decode(file_get_contents("php://input"), true);
    $sellerId   = intval($data["sellerId"] ?? 0);
    $nombre     = trim($data["nombre"] ?? "");
    $descripcion= trim($data["descripcion"] ?? "");
    $precio     = intval($data["precio"] ?? 0);
    $stock      = intval($data["stock"] ?? 0);
    $imageData  = $data["imageData"] ?? null;

    if (!$sellerId || !$nombre || !$descripcion || $precio < 1 || $stock < 1) {
        echo json_encode(["error" => "Completa los campos y agrega al menos una unidad al stock"]); exit;
    }
    $stmt = $conn->prepare("SELECT rol FROM users WHERE id = ?");
    $stmt->bind_param("i", $sellerId);
    $stmt->execute();
    $vendedor = $stmt->get_result()->fetch_assoc();
    if (!$vendedor || !in_array($vendedor["rol"], ["Vendedor","Ambos"])) {
        http_response_code(403); echo json_encode(["error" => "El usuario no puede vender"]); exit;
    }
    $now = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO products (seller_id, nombre, descripcion, precio, stock, image_data, is_active, promoted_until, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NULL, ?)");
    $stmt->bind_param("issiiis", $sellerId, $nombre, $descripcion, $precio, $stock, $imageData, $now);
    $stmt->execute();
    $newId = $conn->insert_id;
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $newId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    echo json_encode(["product" => $product]); exit;
}

// PUT editar producto
if ($method === "PUT" && $id) {
    $data        = json_decode(file_get_contents("php://input"), true);
    $sellerId    = intval($data["sellerId"] ?? 0);
    $nombre      = trim($data["nombre"] ?? "");
    $descripcion = trim($data["descripcion"] ?? "");
    $precio      = intval($data["precio"] ?? 0);
    $stock       = intval($data["stock"] ?? 0);
    $imageData   = $data["imageData"] ?? null;

    $stmt = $conn->prepare("SELECT seller_id, deleted_at FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    if (!$product) { http_response_code(404); echo json_encode(["error" => "Producto no encontrado"]); exit; }
    if ($product["seller_id"] != $sellerId) { http_response_code(403); echo json_encode(["error" => "No autorizado"]); exit; }
    if ($product["deleted_at"] !== null) { http_response_code(404); echo json_encode(["error" => "Producto retirado"]); exit; }
    if (!$nombre || !$descripcion || $precio < 1 || $stock < 1) {
        echo json_encode(["error" => "Completa los campos y agrega al menos una unidad al stock"]); exit;
    }

    $stmt = $conn->prepare("UPDATE products SET nombre=?, descripcion=?, precio=?, stock=?, image_data=?, is_active=1 WHERE id=? AND deleted_at IS NULL");
    $stmt->bind_param("ssiisi", $nombre, $descripcion, $precio, $stock, $imageData, $id);
    $stmt->execute();
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    echo json_encode(["product" => $product]); exit;
}

// DELETE eliminar producto
if ($method === "DELETE" && $id) {
    $data     = json_decode(file_get_contents("php://input"), true);
    $sellerId = intval($data["sellerId"] ?? 0);

    $stmt = $conn->prepare("SELECT seller_id FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    if (!$product) { http_response_code(404); echo json_encode(["error" => "Producto no encontrado"]); exit; }
    if ($product["seller_id"] != $sellerId) { http_response_code(403); echo json_encode(["error" => "No autorizado"]); exit; }

    $stmt = $conn->prepare("UPDATE products SET is_active = 0, deleted_at = COALESCE(deleted_at, NOW()) WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(["ok" => true]); exit;
}

// POST promocionar
if ($method === "POST" && $id && $action === "promote") {
    $data     = json_decode(file_get_contents("php://input"), true);
    $sellerId = intval($data["sellerId"] ?? 0);

    $stmt = $conn->prepare("SELECT seller_id FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    if (!$product || $product["seller_id"] != $sellerId) { http_response_code(403); echo json_encode(["error" => "No autorizado"]); exit; }

    $stmt = $conn->prepare("SELECT monedas FROM users WHERE id = ?");
    $stmt->bind_param("i", $sellerId);
    $stmt->execute();
    $seller = $stmt->get_result()->fetch_assoc();
    if (!$seller || $seller["monedas"] < 10) { echo json_encode(["error" => "Monedas insuficientes"]); exit; }

    $until = date("Y-m-d H:i:s", strtotime("+7 days"));
    $stmt  = $conn->prepare("UPDATE users SET monedas = monedas - 10 WHERE id = ?");
    $stmt->bind_param("i", $sellerId); $stmt->execute();
    $stmt  = $conn->prepare("UPDATE products SET promoted_until = ? WHERE id = ?");
    $stmt->bind_param("si", $until, $id); $stmt->execute();

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id); $stmt->execute();
    $updatedProduct = $stmt->get_result()->fetch_assoc();
    $stmt = $conn->prepare("SELECT id, nombre, curso, rol, contacto, monedas, card_code, is_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $sellerId); $stmt->execute();
    $updatedSeller = $stmt->get_result()->fetch_assoc();

    echo json_encode(["product" => $updatedProduct, "seller" => $updatedSeller]); exit;
}

echo json_encode(["error" => "Ruta no encontrada"]);
