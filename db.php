<?php
$host = getenv("DB_HOST") ?: "localhost";
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASS") ?: "";
$db   = getenv("DB_NAME") ?: "mercado_digital";
$port = (int)(getenv("DB_PORT") ?: 3306);
$sslMode = getenv("DB_SSL") ?: "off";
$sslCa = getenv("DB_SSL_CA") ?: "/etc/ssl/certs/ca-certificates.crt";

if (!in_array($sslMode, ["off", "verify_ca"], true)) {
    http_response_code(500);
    error_log("Invalid DB_SSL configuration.");
    echo json_encode(["error" => "La configuración segura de la base de datos no es válida."]);
    exit;
}

if ($sslMode === "verify_ca" && !is_file($sslCa)) {
    http_response_code(500);
    error_log("Database TLS CA bundle not found.");
    echo json_encode(["error" => "No se encontró el certificado TLS de la base de datos."]);
    exit;
}

try {
    $conn = mysqli_init();
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

    $flags = 0;
    if ($sslMode === "verify_ca") {
        $conn->ssl_set(null, null, $sslCa, null, null);
        $flags = MYSQLI_CLIENT_SSL;
    }

    $conn->real_connect($host, $user, $pass, $db, $port, null, $flags);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(["error" => "No se pudo conectar con la base de datos."]);
    exit;
}

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit;
}
