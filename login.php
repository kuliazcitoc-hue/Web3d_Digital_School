<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require "db.php";

$data     = json_decode(file_get_contents("php://input"), true);
$nombre   = trim($data["nombre"] ?? "");
$password = $data["password"] ?? "";

if (!$nombre || !$password) {
    echo json_encode(["error" => "Faltan campos"]); exit;
}

$stmt = $conn->prepare("SELECT id, nombre, curso, rol, contacto, monedas, card_code, is_admin, password FROM users WHERE nombre = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || (!password_verify($password, $user["password"]) && !hash_equals($user["password"], $password))) {
    http_response_code(401);
    echo json_encode(["error" => "Credenciales inválidas"]); exit;
}

if (!password_get_info($user["password"])["algo"]) {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $passwordHash, $user["id"]);
    $stmt->execute();
}

unset($user["password"]);

echo json_encode(["user" => $user]);
