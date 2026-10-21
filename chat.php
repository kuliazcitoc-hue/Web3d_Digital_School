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
        $stmt = $conn->prepare("SELECT buyer_id, seller_id FROM conversations WHERE id = ?");
        $stmt->bind_param("i", $conversationId);
        $stmt->execute();
        $conversation = $stmt->get_result()->fetch_assoc();
        if (!$conversation || ($userId != $conversation["buyer_id"] && $userId != $conversation["seller_id"])) {
            http_response_code(403); echo json_encode(["error" => "No autorizado"]); exit;
        }

        $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND receiver_id = ? AND is_read = 0");
        $stmt->bind_param("ii", $conversationId, $userId);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT m.id, m.conversation_id, m.sender_id, m.receiver_id, m.message, m.image_data, m.created_at, m.is_read, u.nombre AS sender_nombre FROM messages m JOIN users u ON u.id = m.sender_id WHERE m.conversation_id = ? ORDER BY m.created_at ASC");
        $stmt->bind_param("i", $conversationId);
        $stmt->execute();
        $rows = $stmt->get_result();
        $messages = [];
        while ($r = $rows->fetch_assoc()) $messages[] = $r;
        echo json_encode(["messages" => $messages]); exit;
    }

    $stmt = $conn->prepare("SELECT c.id, c.product_id, c.buyer_id, c.seller_id, c.updated_at, p.nombre AS product_nombre, u_b.nombre AS buyer_nombre, u_s.nombre AS seller_nombre, (SELECT COUNT(*) FROM messages m WHERE m.conversation_id = c.id AND m.receiver_id = ? AND m.is_read = 0) AS unread_count FROM conversations c JOIN products p ON p.id = c.product_id JOIN users u_b ON u_b.id = c.buyer_id JOIN users u_s ON u_s.id = c.seller_id WHERE c.buyer_id = ? OR c.seller_id = ? ORDER BY c.updated_at DESC");
    $stmt->bind_param("iii", $userId, $userId, $userId);
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
        $rawMessage = $data["message"] ?? "";
        if (!is_string($rawMessage)) {
            http_response_code(400); echo json_encode(["error" => "El mensaje no es válido"]); exit;
        }
        $message = trim($rawMessage);

        if (!$conversationId || !$senderId) {
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

        $imageData = $data["imageData"] ?? null;
        if ($imageData !== null) {
            if (!is_string($imageData) || strlen($imageData) > 2097152
                || !preg_match('/^data:image\/(jpeg|png|webp);base64,([A-Za-z0-9+\/]+={0,2})$/', $imageData, $matches)) {
                http_response_code(400); echo json_encode(["error" => "La imagen no es válida o supera el tamaño permitido"]); exit;
            }
            $imageBytes = base64_decode($matches[2], true);
            $imageInfo = $imageBytes === false ? false : getimagesizefromstring($imageBytes);
            $allowedMimes = ["image/jpeg", "image/png", "image/webp"];
            if ($imageInfo === false || !in_array($imageInfo["mime"] ?? "", $allowedMimes, true)) {
                http_response_code(400); echo json_encode(["error" => "El archivo enviado no es una imagen válida"]); exit;
            }
        }

        if ($message === "" && $imageData === null) {
            http_response_code(400); echo json_encode(["error" => "Escribe un mensaje o adjunta una foto"]); exit;
        }

        $receiverId = ($senderId == $conversation["buyer_id"]) ? $conversation["seller_id"] : $conversation["buyer_id"];
        $now = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, receiver_id, message, image_data, created_at, is_read) VALUES (?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("iiisss", $conversationId, $senderId, $receiverId, $message, $imageData, $now);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE conversations SET updated_at = ? WHERE id = ?");
        $stmt->bind_param("si", $now, $conversationId);
        $stmt->execute();

        echo json_encode(["ok" => true]); exit;
    }
}

echo json_encode(["error" => "Acción no encontrada"]);
