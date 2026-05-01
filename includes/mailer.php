<?php
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Loads SMTP settings from the database.
 * Returns an array with the settings, or null if not configured.
 */
function getSmtpSettings() {
    // $conn is a global variable set by connection.php
    // We must declare it global to access it inside a function scope
    require_once __DIR__ . '/../db/connection.php';
    global $conn;
    if (!$conn) return null;
    $result = $conn->query("SELECT * FROM smtp_settings ORDER BY id ASC LIMIT 1");
    if (!$result) return null;
    $row = $result->fetch_assoc();
    if (!$row || empty($row['smtp_username']) || empty($row['smtp_password'])) {
        return null; // Not configured yet
    }
    return $row;
}

/**
 * Sends a verification email using SMTP settings from the database.
 * Returns true on success, false on failure.
 */
function sendVerificationEmail($toEmail, $toName, $code) {
    $settings = getSmtpSettings();

    // If no SMTP settings configured — log and skip (code is shown in session fallback)
    if (!$settings) {
        error_log("[Wasla Mailer] SMTP not configured. Visit /db/setup_email.php then configure in Admin → Email Settings.");
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        // ── Server ────────────────────────────────────────────────────────────
        $mail->isSMTP();
        $mail->Host       = $settings['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $settings['smtp_username'];
        $mail->Password   = $settings['smtp_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)$settings['smtp_port'];
        $mail->Timeout    = 10;
        $mail->CharSet    = 'UTF-8';

        // ── Recipients ────────────────────────────────────────────────────────
        $mail->setFrom($settings['smtp_from_email'], $settings['smtp_from_name']);
        $mail->addAddress($toEmail, $toName);

        // ── Content ───────────────────────────────────────────────────────────
        $mail->isHTML(true);
        $mail->Subject = 'Wasla - Your Verification Code';
        $mail->Body = "
            <div style='font-family:Inter,Arial,sans-serif;max-width:480px;margin:auto;background:#0d1b2e;border-radius:16px;overflow:hidden;'>
                <div style='background:linear-gradient(135deg,#1a3a5c,#0d1b2e);padding:32px;text-align:center;'>
                    <h1 style='color:#00c9a7;font-size:2rem;margin:0;letter-spacing:-1px;'>Wasla</h1>
                    <p style='color:rgba(255,255,255,0.5);margin:4px 0 0;font-size:0.85rem;'>Email Verification</p>
                </div>
                <div style='padding:32px;'>
                    <p style='color:rgba(255,255,255,0.8);font-size:1rem;margin:0 0 24px;'>Hi <strong style='color:#fff;'>{$toName}</strong>,</p>
                    <p style='color:rgba(255,255,255,0.7);margin:0 0 24px;'>Your Wasla verification code is:</p>
                    <div style='background:rgba(0,201,167,0.1);border:2px solid #00c9a7;border-radius:12px;padding:24px;text-align:center;margin-bottom:24px;'>
                        <span style='font-size:2.5rem;font-weight:900;color:#00c9a7;letter-spacing:12px;'>{$code}</span>
                    </div>
                    <p style='color:rgba(255,255,255,0.5);font-size:0.85rem;margin:0;'>This code expires in <strong>10 minutes</strong>.<br>If you didn't request this, please ignore this email.</p>
                </div>
                <div style='background:rgba(255,255,255,0.04);padding:16px 32px;text-align:center;'>
                    <p style='color:rgba(255,255,255,0.3);font-size:0.75rem;margin:0;'>&copy; 2024 Wasla Digital Conduit. All rights reserved.</p>
                </div>
            </div>
        ";
        $mail->AltBody = "Your Wasla verification code is: {$code}\n\nThis code expires in 10 minutes.\nIf you didn't request this, please ignore this email.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("[Wasla Mailer] Failed to send to {$toEmail}: {$mail->ErrorInfo}");
        return false;
    }
}
