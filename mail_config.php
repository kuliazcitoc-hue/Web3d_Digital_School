<?php
define("APP_BASE_URL", getenv("APP_BASE_URL") ?: (getenv("RENDER_EXTERNAL_URL") ?: "http://localhost/mercado_digital"));
define("MAIL_FROM", getenv("MAIL_FROM") ?: "digitalmarketdevice@gmail.com");
define("MAIL_FROM_NAME", getenv("MAIL_FROM_NAME") ?: "Mercado Digital");

function sendAppEmail(string $recipient, string $subject, string $message): bool
{
    $transport = getenv("MAIL_TRANSPORT") ?: "mail";

    if ($transport === "brevo") {
        $apiKey = getenv("BREVO_API_KEY") ?: "";
        if (!$apiKey) {
            error_log("Brevo email configuration is incomplete.");
            return false;
        }

        $payload = json_encode([
            "sender" => ["name" => MAIL_FROM_NAME, "email" => MAIL_FROM],
            "to" => [["email" => $recipient]],
            "subject" => $subject,
            "textContent" => $message,
        ]);
        if ($payload === false) {
            error_log("Could not encode the Brevo email request.");
            return false;
        }

        $context = stream_context_create([
            "http" => [
                "method" => "POST",
                "header" => "accept: application/json\r\n"
                    . "api-key: " . $apiKey . "\r\n"
                    . "content-type: application/json\r\n",
                "content" => $payload,
                "timeout" => 15,
                "ignore_errors" => true,
                "follow_location" => 0,
                "max_redirects" => 0,
            ],
        ]);
        $response = @file_get_contents("https://api.brevo.com/v3/smtp/email", false, $context);
        $status = 0;
        foreach ($http_response_header ?? [] as $header) {
            if (preg_match("/^HTTP\\/\\S+\\s+(\\d{3})/", $header, $matches)) {
                $status = (int)$matches[1];
                break;
            }
        }

        if ($response === false || $status < 200 || $status >= 300) {
            error_log("Brevo email delivery failed (HTTP " . $status . ").");
            return false;
        }

        return true;
    }

    if ($transport === "mail") {
        $headers = "From: " . MAIL_FROM . "\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";
        if (!mail($recipient, $subject, $message, $headers)) {
            error_log("Password reset email could not be sent with PHP mail().");
            return false;
        }

        return true;
    }

    if ($transport !== "smtp") {
        error_log("Invalid MAIL_TRANSPORT configuration.");
        return false;
    }

    $host = getenv("SMTP_HOST") ?: "";
    $port = (int)(getenv("SMTP_PORT") ?: 587);
    $username = getenv("SMTP_USER") ?: "";
    $password = getenv("SMTP_PASS") ?: "";
    $secure = getenv("SMTP_SECURE") ?: "tls";

    if (!$host || !$username || !$password || $port < 1 || $port > 65535 || !in_array($secure, ["tls", "ssl"], true)) {
        error_log("SMTP email configuration is incomplete or invalid.");
        return false;
    }

    $autoload = __DIR__ . "/vendor/autoload.php";
    if (!is_file($autoload)) {
        error_log("SMTP email dependency is missing; install Composer dependencies.");
        return false;
    }

    require_once $autoload;

    try {
        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $host;
        $mailer->SMTPAuth = true;
        $mailer->Username = $username;
        $mailer->Password = $password;
        $mailer->SMTPSecure = $secure === "ssl"
            ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
            : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->Port = $port;
        $mailer->Timeout = 15;
        $mailer->CharSet = \PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
        $mailer->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mailer->addAddress($recipient);
        $mailer->Subject = $subject;
        $mailer->Body = $message;

        return $mailer->send();
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Password reset SMTP delivery failed: " . $e->getMessage());
        return false;
    }
}