<?php
// ============================================
// Send Password Reset Link via PHPMailer
// ============================================
header('Content-Type: application/json');
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/../includes/mailer.php';

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');

if (empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Please enter your email address.']);
    exit;
}

// Check if user exists
$stmt = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Don't reveal whether the email exists (security best practice)
    echo json_encode(['success' => true, 'message' => 'If that email is registered, a reset link has been sent. Check your inbox.']);
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Generate a secure token
$token = bin2hex(random_bytes(32)); // 64-character hex token
$expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

// Save token to database
$stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
$stmt->bind_param("ssi", $token, $expires, $user['id']);
$stmt->execute();
$stmt->close();

// Build reset link
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
$resetLink = "{$protocol}://{$host}{$basePath}/reset_password.php?token={$token}";

// Send email via PHPMailer
$toName = trim($user['first_name'] . ' ' . $user['last_name']);
$settings = getSmtpSettings();

if (!$settings) {
    echo json_encode(['success' => false, 'error' => 'Email system is not configured. Contact admin.']);
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = $settings['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $settings['smtp_username'];
    $mail->Password   = $settings['smtp_password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)$settings['smtp_port'];
    $mail->Timeout    = 10;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($settings['smtp_from_email'], $settings['smtp_from_name']);
    $mail->addAddress($email, $toName);

    $mail->isHTML(true);
    $mail->Subject = 'Wasla - Password Reset';
    $mail->Body = "
        <div style='font-family:Inter,Arial,sans-serif;max-width:480px;margin:auto;background:#0d1b2e;border-radius:16px;overflow:hidden;'>
            <div style='background:linear-gradient(135deg,#1a3a5c,#0d1b2e);padding:32px;text-align:center;'>
                <h1 style='color:#00c9a7;font-size:2rem;margin:0;letter-spacing:-1px;'>Wasla</h1>
                <p style='color:rgba(255,255,255,0.5);margin:4px 0 0;font-size:0.85rem;'>Password Reset</p>
            </div>
            <div style='padding:32px;'>
                <p style='color:rgba(255,255,255,0.8);font-size:1rem;margin:0 0 24px;'>Hi <strong style='color:#fff;'>{$toName}</strong>,</p>
                <p style='color:rgba(255,255,255,0.7);margin:0 0 24px;'>We received a request to reset your password. Click the button below:</p>
                <div style='text-align:center;margin-bottom:24px;'>
                    <a href='{$resetLink}' style='display:inline-block;padding:14px 36px;background:linear-gradient(135deg,#00c9a7,#00e676);color:#fff;font-weight:700;font-size:1rem;border-radius:10px;text-decoration:none;'>Reset My Password</a>
                </div>
                <p style='color:rgba(255,255,255,0.5);font-size:0.85rem;margin:0 0 12px;'>Or copy and paste this link into your browser:</p>
                <p style='color:#4fc3f7;font-size:0.8rem;word-break:break-all;margin:0 0 24px;'>{$resetLink}</p>
                <p style='color:rgba(255,255,255,0.5);font-size:0.85rem;margin:0;'>This link expires in <strong>30 minutes</strong>.<br>If you didn't request this, please ignore this email.</p>
            </div>
            <div style='background:rgba(255,255,255,0.04);padding:16px 32px;text-align:center;'>
                <p style='color:rgba(255,255,255,0.3);font-size:0.75rem;margin:0;'>&copy; 2024 Wasla Digital Conduit. All rights reserved.</p>
            </div>
        </div>
    ";
    $mail->AltBody = "Reset your Wasla password by visiting: {$resetLink}\n\nThis link expires in 30 minutes.";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'If that email is registered, a reset link has been sent. Check your inbox.']);
} catch (Exception $e) {
    error_log("[Wasla Mailer] Reset email failed: {$mail->ErrorInfo}");
    echo json_encode(['success' => false, 'error' => 'Failed to send reset email. Please try again later.']);
}
