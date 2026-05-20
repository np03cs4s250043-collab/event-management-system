<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

function sendOtpEmail(string $toEmail, string $toName, string $otp): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = defined('SMTP_USER') ? SMTP_USER : '';
        $mail->Password   = defined('SMTP_PASS') ? SMTP_PASS : '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;

        $mail->setFrom(defined('SMTP_FROM') ? SMTP_FROM : SMTP_USER, APP_NAME);
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Password Reset OTP - ' . APP_NAME;
        $mail->Body    = otpEmailBody($toName, $otp);
        $mail->AltBody = "Your " . APP_NAME . " password reset OTP is: $otp\nIt expires in 10 minutes.";

        return $mail->send();
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}

function otpEmailBody(string $name, string $otp): string {
    $appName = APP_NAME;
    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f4f4f7">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7;padding:40px 0">
    <tr><td align="center">
      <table width="480" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)">
        <tr><td style="background:linear-gradient(135deg,#e63946,#c1121f);padding:32px 40px;text-align:center">
          <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:800;letter-spacing:-0.5px">{$appName}</h1>
          <p style="margin:6px 0 0;color:rgba(255,255,255,.85);font-size:13px">Password Reset</p>
        </td></tr>
        <tr><td style="padding:40px">
          <p style="margin:0 0 16px;color:#333;font-size:15px">Hi <strong>{$name}</strong>,</p>
          <p style="margin:0 0 24px;color:#555;font-size:14px;line-height:1.6">
            We received a request to reset your password. Use the OTP below. It is valid for <strong>10 minutes</strong>.
          </p>
          <div style="background:#f8f0f1;border:2px dashed #e63946;border-radius:10px;padding:20px;text-align:center;margin:0 0 24px">
            <p style="margin:0 0 6px;color:#999;font-size:11px;text-transform:uppercase;letter-spacing:1px">Your OTP</p>
            <span style="font-size:40px;font-weight:900;color:#c1121f;letter-spacing:10px">{$otp}</span>
          </div>
          <p style="margin:0 0 16px;color:#777;font-size:13px;line-height:1.6">
            If you did not request a password reset, please ignore this email. Your account remains secure.
          </p>
          <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
          <p style="margin:0;color:#aaa;font-size:12px;text-align:center">&copy; {$appName} — Do not reply to this email.</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}
