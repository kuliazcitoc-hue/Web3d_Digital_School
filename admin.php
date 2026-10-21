<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require "db.php";

$method = $_SERVER["REQUEST_METHOD"];
$action = $_GET["action"] ?? "";

function requireAdmin($conn, $adminId) {
    if (!$adminId) { http_response_code(403); echo json_encode(["error" => "Falta adminId"]); exit; }
    $stmt = $conn->prepare("SELECT id, is_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $adminId); $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user || $user["is_admin"] != 1) { http_response_code(403); echo json_encode(["error" => "No autorizado"]); exit; }
    return $user;
}

function nowStr() { return date("Y-m-d H:i:s"); }

// ── Cargar saldo ──────────────────────────────────────────────────────────────
if ($action === "credit" && $method === "POST") {
    $data     = json_decode(file_get_contents("php://input"), true);
    $adminId  = intval($data["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $cardCode = strtoupper(str_replace("-", "", trim($data["cardCode"] ?? "")));
    $amount   = intval($data["amount"] ?? 0);
    $note     = $data["note"] ?? "Recarga semanal";
    if (!$cardCode || $amount < 1) { echo json_encode(["error" => "Datos inválidos"]); exit; }
    $stmt = $conn->prepare("SELECT id, nombre FROM users WHERE REPLACE(card_code, '-', '') = ?");
    $stmt->bind_param("s", $cardCode); $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) { http_response_code(404); echo json_encode(["error" => "Tarjeta no encontrada"]); exit; }
    $stmt = $conn->prepare("UPDATE users SET monedas = monedas + ? WHERE id = ?");
    $stmt->bind_param("ii", $amount, $user["id"]); $stmt->execute();
    $now = nowStr();
    $stmt = $conn->prepare("INSERT INTO credits (user_id, amount, note, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user["id"], $amount, $note, $now); $stmt->execute();
    $stmt = $conn->prepare("SELECT id, nombre, curso, rol, contacto, monedas, card_code FROM users WHERE id = ?");
    $stmt->bind_param("i", $user["id"]); $stmt->execute();
    $updated = $stmt->get_result()->fetch_assoc();
    echo json_encode(["user" => $updated]); exit;
}

// ── Comisiones ────────────────────────────────────────────────────────────────
if ($action === "commissions" && $method === "GET") {
    $adminId = intval($_GET["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $row = $conn->query("SELECT total_comisiones FROM admin WHERE id = 1")->fetch_assoc();
    echo json_encode(["total" => $row["total_comisiones"] ?? 0]); exit;
}

// ── Promocionados ─────────────────────────────────────────────────────────────
if ($action === "promoted" && $method === "GET") {
    $adminId = intval($_GET["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $rows = $conn->query("SELECT p.*, u.nombre AS vendedor_nombre FROM products p JOIN users u ON u.id = p.seller_id WHERE p.promoted_until IS NOT NULL AND p.promoted_until >= NOW() AND p.is_active = 1 ORDER BY p.promoted_until DESC");
    $products = [];
    while ($r = $rows->fetch_assoc()) $products[] = $r;
    echo json_encode(["products" => $products]); exit;
}

// ── Stats ─────────────────────────────────────────────────────────────────────
if ($action === "stats" && $method === "GET") {
    $adminId = intval($_GET["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $users  = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];
    $admins = $conn->query("SELECT COUNT(*) AS total FROM users WHERE is_admin = 1")->fetch_assoc()["total"];
    echo json_encode(["users" => $users, "admins" => $admins]); exit;
}

if ($action === "users" && $method === "GET") {
    $adminId = intval($_GET["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $rows = $conn->query("SELECT u.id, u.nombre, u.curso, u.rol, u.contacto, u.monedas, u.card_code, u.is_admin, COUNT(p.id) AS product_count FROM users u LEFT JOIN products p ON p.seller_id = u.id GROUP BY u.id, u.nombre, u.curso, u.rol, u.contacto, u.monedas, u.card_code, u.is_admin ORDER BY u.nombre ASC");
    $users = [];
    while ($r = $rows->fetch_assoc()) $users[] = $r;
    echo json_encode(["users" => $users]); exit;
}

if ($action === "update_password" && $method === "POST") {
    $data     = json_decode(file_get_contents("php://input"), true);
    $adminId  = intval($data["adminId"] ?? 0);
    $userId   = intval($data["userId"] ?? 0);
    $password = $data["password"] ?? "";
    requireAdmin($conn, $adminId);
    if (!$userId || mb_strlen($password) < 4) { echo json_encode(["error" => "La contraseña debe tener al menos 4 caracteres"]); exit; }
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $passwordHash, $userId); $stmt->execute();
    if ($stmt->affected_rows < 1) { http_response_code(404); echo json_encode(["error" => "Usuario no encontrado o contraseña sin cambios"]); exit; }
    echo json_encode(["ok" => true]); exit;
}

if ($action === "delete_user" && $method === "POST") {
    $data    = json_decode(file_get_contents("php://input"), true);
    $adminId = intval($data["adminId"] ?? 0);
    $userId  = intval($data["userId"] ?? 0);
    requireAdmin($conn, $adminId);
    if (!$userId) { echo json_encode(["error" => "Falta el usuario"]); exit; }
    if ($userId === $adminId) { http_response_code(400); echo json_encode(["error" => "No puedes eliminar tu propia cuenta"]); exit; }
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("DELETE FROM purchases WHERE buyer_id = ? OR seller_id = ?");
        $stmt->bind_param("ii", $userId, $userId); $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM credits WHERE user_id = ?");
        $stmt->bind_param("i", $userId); $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM products WHERE seller_id = ?");
        $stmt->bind_param("i", $userId); $stmt->execute();
        $stmt = $conn->prepare("UPDATE invite_codes SET used_by = NULL WHERE used_by = ?");
        $stmt->bind_param("i", $userId); $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId); $stmt->execute();
        if ($stmt->affected_rows < 1) throw new Exception("Usuario no encontrado");
        $conn->commit();
        echo json_encode(["ok" => true]); exit;
    } catch (Throwable $e) {
        $conn->rollback();
        http_response_code(500); echo json_encode(["error" => "No se pudo eliminar el usuario"]); exit;
    }
}

// ── Códigos de invitación ─────────────────────────────────────────────────────
if ($action === "invites" && $method === "GET") {
    $adminId = intval($_GET["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $rows  = $conn->query("SELECT code FROM invite_codes WHERE used = 0 ORDER BY code ASC");
    $codes = [];
    while ($r = $rows->fetch_assoc()) $codes[] = $r["code"];
    echo json_encode(["codes" => $codes]); exit;
}

// ── Agregar códigos ───────────────────────────────────────────────────────────
if ($action === "invites_add" && $method === "POST") {
    $data    = json_decode(file_get_contents("php://input"), true);
    $adminId = intval($data["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $count = intval($data["count"] ?? 0);
    if ($count < 1 || $count > 500) { echo json_encode(["error" => "Cantidad inválida"]); exit; }
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $added = 0;
    while ($added < $count) {
        $part = "";
        for ($i = 0; $i < 6; $i++) $part .= $chars[random_int(0, strlen($chars)-1)];
        $code = "MD-" . $part;
        $st   = $conn->prepare("INSERT IGNORE INTO invite_codes (code, used) VALUES (?, 0)");
        $st->bind_param("s", $code); $st->execute();
        if ($conn->affected_rows > 0) $added++;
    }
    echo json_encode(["added" => $added]); exit;
}

// ── Historial recargas ────────────────────────────────────────────────────────
if ($action === "credits" && $method === "GET") {
    $adminId = intval($_GET["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $rows = $conn->query("SELECT c.id, c.user_id, c.amount, c.note, c.created_at, u.nombre AS usuario FROM credits c JOIN users u ON u.id = c.user_id ORDER BY c.created_at DESC LIMIT 100");
    $credits = [];
    while ($r = $rows->fetch_assoc()) $credits[] = $r;
    echo json_encode(["credits" => $credits]); exit;
}

// ── Eliminar recarga ──────────────────────────────────────────────────────────
if ($action === "delete_credit" && $method === "POST") {
    $data    = json_decode(file_get_contents("php://input"), true);
    $adminId = intval($data["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $id   = intval($data["id"] ?? 0);
    $stmt = $conn->prepare("DELETE FROM credits WHERE id = ?");
    $stmt->bind_param("i", $id); $stmt->execute();
    echo json_encode(["ok" => true]); exit;
}

// ── Reinicio total ────────────────────────────────────────────────────────────
if ($action === "reset" && $method === "POST") {
    $data    = json_decode(file_get_contents("php://input"), true);
    $adminId = intval($data["adminId"] ?? 0);
    requireAdmin($conn, $adminId);
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("DELETE FROM purchases");
    $conn->query("DELETE FROM credits");
    $conn->query("DELETE FROM products");
    $conn->query("DELETE FROM users");
    $conn->query("UPDATE admin SET total_comisiones = 0 WHERE id = 1");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    echo json_encode(["ok" => true]); exit;
}

echo json_encode(["error" => "Acción no encontrada"]);
