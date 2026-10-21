<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require "db.php";

$id = intval($_GET["id"] ?? 0);
if (!$id) { echo json_encode(["error" => "Falta id"]); exit; }

$stmt = $conn->prepare("SELECT id, nombre, curso, rol, contacto, monedas, card_code, is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) { http_response_code(404); echo json_encode(["error" => "Usuario no encontrado"]); exit; }

echo json_encode(["user" => $user]);
