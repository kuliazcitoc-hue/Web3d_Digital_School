<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require "db.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $userId = intval($_GET["userId"] ?? 0);
    $conversationId = intval($_GET["conversationId"] ?? 0);

    if (!$userId) {
        echo json_encode(["error" => "Falta userId"]); exit;
    }

    if ($conversationId) {
        $stmt = $conn->prepare("SELECT m.id, m.conversation_id, m.sender_id, m.receiver_id, m.message, m.created_at, m.is_read, u.nombre AS sender_nombre FROM messages m JOIN users u ON u.id = m.sender_id WHERE m.conversation_id = ? ORDER BY m.created_at ASC");
        $stmt->bind_param("i", $conversationId);
        $stmt->execute();
        $rows = $stmt->get_result();
        $messages = [];
        while ($r = $rows->fetch_assoc()) $messages[] = $r;
        echo json_encode(["messages" => $messages]); exit;
    }

    $stmt = $conn->prepare("SELECT c.id, c.product_id, c.buyer_id, c.seller_id, c.updated_at, p.nombre AS product_nombre, u_b.nombre AS buyer_nombre, u_s.nombre AS seller_nombre FROM conversations c JOIN products p ON p.id = c.product_id JOIN users u_b ON u_b.id = c.buyer_id JOIN users u_s ON u_s.id = c.seller_id WHERE c.buyer_id = ? OR c.seller_id = ? ORDER BY c.updated_at DESC");
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    $rows = $stmt->get_result();
    $conversations = [];
    while ($r = $rows->fetch_assoc()) $conversations[] = $r;
    echo json_encode(["conversations" => $conversations]); exit;
}

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $_GET["action"] ?? "";

    if ($action === "create") {
        $productId = intval($data["productId"] ?? 0);
        $buyerId = intval($data["buyerId"] ?? 0);

        if (!$productId || !$buyerId) {
            echo json_encode(["error" => "Datos inválidos"]); exit;
        }

        $stmt = $conn->prepare("SELECT seller_id, nombre FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if (!$product) {
            http_response_code(404);
            echo json_encode(["error" => "Producto no encontrado"]); exit;
        }

        $sellerId = intval($product["seller_id"]);
        if ($sellerId === $buyerId) {
            echo json_encode(["error" => "No puedes chatear contigo mismo"]); exit;
        }

        $stmt = $conn->prepare("SELECT id FROM conversations WHERE product_id = ? AND buyer_id = ? AND seller_id = ?");
        $stmt->bind_param("iii", $productId, $buyerId, $sellerId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            echo json_encode(["conversation" => $existing]); exit;
        }

        $now = date("Y-m-d H:i:s");
        $stmt = $conn->prepare("INSERT INTO conversations (product_id, buyer_id, seller_id, created_at, updated_at, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("iiiss", $productId, $buyerId, $sellerId, $now, $now);
        $stmt->execute();

        $conversationId = $conn->insert_id;
        echo json_encode(["conversation" => ["id" => $conversationId]]); exit;
    }

    if ($action === "send") {
        $conversationId = intval($data["conversationId"] ?? 0);
        $senderId = intval($data["senderId"] ?? 0);
        $message = trim($data["message"] ?? "");

        if (!$conversationId || !$senderId || !$message) {
            echo json_encode(["error" => "Faltan datos"]); exit;
        }

        $stmt = $conn->prepare("SELECT buyer_id, seller_id FROM conversations WHERE id = ?");
        $stmt->bind_param("i", $conversationId);
        $stmt->execute();
        $conversation = $stmt->get_result()->fetch_assoc();

        if (!$conversation) {
            echo json_encode(["error" => "Conversación no encontrada"]); exit;
        }

        if (!($senderId == $conversation["buyer_id"] || $senderId == $conversation["seller_id"])) {
            echo json_encode(["error" => "No autorizado"]); exit;
        }

        $receiverId = ($senderId == $conversation["buyer_id"]) ? $conversation["seller_id"] : $conversation["buyer_id"];
        $now = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, receiver_id, message, created_at, is_read) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("iiiss", $conversationId, $senderId, $receiverId, $message, $now);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE conversations SET updated_at = ? WHERE id = ?");
        $stmt->bind_param("si", $now, $conversationId);
        $stmt->execute();

        echo json_encode(["ok" => true]); exit;
    }
}

echo json_encode(["error" => "Acción no encontrada"]);
