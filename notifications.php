<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require "db.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET" && ($_GET["action"] ?? "") === "counts") {
    $userId = intval($_GET["userId"] ?? 0);
    if (!$userId) { http_response_code(400); echo json_encode(["error" => "Falta userId"]); exit; }

    $stmt = $conn->prepare("SELECT last_purchase_seen_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) { http_response_code(404); echo json_encode(["error" => "Usuario no encontrado"]); exit; }

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM purchases WHERE id > ? AND (buyer_id = ? OR seller_id = ?)");
    $stmt->bind_param("iii", $user["last_purchase_seen_id"], $userId, $userId);
    $stmt->execute();
    $purchaseCount = intval($stmt->get_result()->fetch_assoc()["total"] ?? 0);

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $messageCount = intval($stmt->get_result()->fetch_assoc()["total"] ?? 0);

    echo json_encode(["purchases" => $purchaseCount, "messages" => $messageCount]); exit;
}

if ($method === "POST" && ($_GET["action"] ?? "") === "read_purchases") {
    $data = json_decode(file_get_contents("php://input"), true);
    $userId = intval($data["userId"] ?? 0);
    if (!$userId) { http_response_code(400); echo json_encode(["error" => "Falta userId"]); exit; }

    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        http_response_code(404); echo json_encode(["error" => "Usuario no encontrado"]); exit;
    }

    $stmt = $conn->prepare("UPDATE users SET last_purchase_seen_id = COALESCE((SELECT MAX(id) FROM purchases WHERE buyer_id = ? OR seller_id = ?), 0) WHERE id = ?");
    $stmt->bind_param("iii", $userId, $userId, $userId);
    $stmt->execute();
    echo json_encode(["ok" => true]); exit;
}

http_response_code(404);
echo json_encode(["error" => "Acción no encontrada"]);
