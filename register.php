<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$nombre     = trim($data["nombre"] ?? "");
$curso      = $data["curso"] ?? "";
$rol        = $data["rol"] ?? "";
$password   = $data["password"] ?? "";
$email      = strtolower(trim($data["email"] ?? ""));
$contacto   = trim($data["contacto"] ?? "");
$verifyCode = strtoupper(trim(is_string($data["verifyCode"] ?? null) ? $data["verifyCode"] : ""));
$adminCode  = is_string($data["adminCode"] ?? null) ? $data["adminCode"] : "";

$cursosPermitidos = ["10-1","10-2","10-3","11-1","11-2"];
$rolesPermitidos  = ["Comprador","Vendedor","Ambos"];
$ADMIN_CODE       = getenv("ADMIN_SIGNUP_CODE") ?: "";
$TEMP_CODE        = getenv("TEMP_SIGNUP_CODE") ?: "";

if (!$nombre || !$curso || !$rol || !$password || !$email || !$contacto) {
    echo json_encode(["error" => "Faltan campos"]); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Correo electrónico inválido"]); exit;
}
if (mb_strlen($password) < 8) {
    echo json_encode(["error" => "La contraseña debe tener al menos 8 caracteres"]); exit;
}
if (mb_strlen($nombre) < 10) {
    echo json_encode(["error" => "El nombre debe tener al menos 10 caracteres"]); exit;
}
if (!in_array($curso, $cursosPermitidos)) {
    echo json_encode(["error" => "Curso no permitido"]); exit;
}
if (!in_array($rol, $rolesPermitidos)) {
    echo json_encode(["error" => "Rol no permitido"]); exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE nombre = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["error" => "Nombre ya registrado"]); exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["error" => "Correo electrónico ya registrado"]); exit;
}

$isTemporaryCode = $TEMP_CODE !== "" && hash_equals($TEMP_CODE, $verifyCode);

// Validar código de invitación
if (!$isTemporaryCode) {
    $stmt = $conn->prepare("SELECT code, used FROM invite_codes WHERE code = ?");
    $stmt->bind_param("s", $verifyCode);
    $stmt->execute();
    $invite = $stmt->get_result()->fetch_assoc();
    if (!$invite || $invite["used"] == 1) {
        echo json_encode(["error" => "Código de verificación inválido"]); exit;
    }
}

// Generar card_code único
$cursoClean = str_replace("-", "", $curso);
$chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
$cardCode = "";
for ($t = 0; $t < 5; $t++) {
    $part = "";
    for ($i = 0; $i < 4; $i++) $part .= $chars[random_int(0, strlen($chars)-1)];
    $candidate = "MD" . $cursoClean . $part;
    $st = $conn->prepare("SELECT id FROM users WHERE card_code = ?");
    $st->bind_param("s", $candidate);
    $st->execute();
    if ($st->get_result()->num_rows === 0) { $cardCode = $candidate; break; }
}
if (!$cardCode) $cardCode = "MD" . $cursoClean . substr(time(), -4);

$isAdmin = ($ADMIN_CODE !== "" && hash_equals($ADMIN_CODE, $adminCode)) ? 1 : 0;
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (nombre, curso, rol, password, email, contacto, monedas, card_code, is_admin) VALUES (?, ?, ?, ?, ?, ?, 50, ?, ?)");
$stmt->bind_param("sssssssi", $nombre, $curso, $rol, $passwordHash, $email, $contacto, $cardCode, $isAdmin);
$stmt->execute();
$userId = $conn->insert_id;

// Marcar código como usado
if (!$isTemporaryCode) {
    $now = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("UPDATE invite_codes SET used = 1, used_by = ?, used_at = ? WHERE code = ?");
    $stmt->bind_param("iss", $userId, $now, $verifyCode);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT id, nombre, curso, rol, contacto, monedas, card_code, is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

echo json_encode(["user" => $user]);
