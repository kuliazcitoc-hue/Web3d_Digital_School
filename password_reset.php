<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

require "db.php";
$data = json_decode(file_get_contents("php://input"), true) ?? [];
$action = $data["action"] ?? "";

if ($action === "request") {
    $email = strtolower(trim($data["email"] ?? ""));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Correo electrónico inválido"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash("sha256", $token);
        $userId = intval($user["id"]);
        $stmt = $conn->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO password_reset_tokens (user_id, token_hash, expires_at, created_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR), NOW())");
        $stmt->bind_param("is", $userId, $tokenHash);
        $stmt->execute();

        require "mail_config.php";
        $resetUrl = rtrim(APP_BASE_URL, "/") . "/index.php?token=" . urlencode($token);
        $subject = "Recuperación de contraseña | Mercado Digital";
        $message = "Hola,\n\nRecibimos una solicitud para cambiar la contraseña de tu cuenta. Abre este enlace dentro de la próxima hora:\n\n" . $resetUrl . "\n\nSi no solicitaste este cambio, ignora este correo.\n";
        $headers = "From: " . MAIL_FROM . "\r\n" . "Content-Type: text/plain; charset=UTF-8\r\n";
        if (!@mail($email, $subject, $message, $headers)) {
            error_log("No se pudo enviar el correo de recuperación para usuario " . $userId);
        }
    }

    echo json_encode(["message" => "Si el correo está registrado, recibirás un enlace para cambiar tu contraseña."]);
    exit;
}

if ($action === "reset") {
    $token = trim($data["token"] ?? "");
    $password = $data["password"] ?? "";
    if (!preg_match("/^[a-f0-9]{64}$/i", $token) || mb_strlen($password) < 8) {
        http_response_code(400);
        echo json_encode(["error" => "Enlace inválido o contraseña demasiado corta"]);
        exit;
    }

    $tokenHash = hash("sha256", $token);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("SELECT id, user_id FROM password_reset_tokens WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW() FOR UPDATE");
        $stmt->bind_param("s", $tokenHash);
        $stmt->execute();
        $reset = $stmt->get_result()->fetch_assoc();
        if (!$reset) {
            $conn->rollback();
            http_response_code(400);
            echo json_encode(["error" => "El enlace expiró o ya fue utilizado. Solicita uno nuevo."]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $passwordHash, $reset["user_id"]);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL");
        $stmt->bind_param("i", $reset["user_id"]);
        $stmt->execute();
        $conn->commit();
        echo json_encode(["ok" => true]);
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar la contraseña"]);
        exit;
    }
}

http_response_code(400);
echo json_encode(["error" => "Acción inválida"]);