<?php
define("APP_BASE_URL", getenv("APP_BASE_URL") ?: (getenv("RENDER_EXTERNAL_URL") ?: "http://localhost/mercado_digital"));
define("MAIL_FROM", getenv("MAIL_FROM") ?: "digitalmarketdevice@gmail.com");
define("MAIL_FROM_NAME", getenv("MAIL_FROM_NAME") ?: "Mercado Digital");

function sendAppEmail(string $recipient, string $subject, string $message): bool
{
    $transport = getenv("MAIL_TRANSPORT") ?: "mail";

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