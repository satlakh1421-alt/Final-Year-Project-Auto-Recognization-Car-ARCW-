<?php

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/PHPMailer/PHPMailer.php";
require_once __DIR__ . "/PHPMailer/SMTP.php";
require_once __DIR__ . "/PHPMailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ===================== RESET EMAIL ===================== */
function send_reset_email($to_email, $reset_link)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to_email);

        $mail->isHTML(true);
        $mail->Subject = "Reset your password - Servpro Autospa";

        $mail->Body = "
            <h3>Password Reset Request</h3>
            <p><a href='{$reset_link}'>{$reset_link}</a></p>
        ";

        return $mail->send();

    } catch (Exception $e) {
        return false;
    }
}


/* ===================== WELCOME EMAIL ===================== */
function send_welcome_email($to_email, $name)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to_email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Welcome to Servpro Autospa";

        $mail->Body = "
            <h2>Welcome {$name}!</h2>
            <p>Your registration is successful.</p>
        ";

        return $mail->send();

    } catch (Exception $e) {
        return false;
    }
}


/* ===================== OTP EMAIL ===================== */
function send_otp_email($to_email, $otp)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to_email);

        $mail->isHTML(true);
        $mail->Subject = "Your OTP Code - Servpro Autospa";

        $mail->Body = "
        <div style='font-family:Arial,sans-serif;background:#f4f4f4;padding:40px;'>
            <div style='max-width:420px;margin:auto;background:#fff;padding:30px;
                        border-radius:10px;text-align:center;'>

                <h2>OTP Verification</h2>

                <p>Your OTP code is:</p>

                <div style='
                font-size:32px;
                letter-spacing:8px;
                font-weight:bold;
                margin:20px 0;
                color:#000;
                white-space:nowrap;
            '>
                {$otp}
            </div>

                <p style='color:red;'>
                    This OTP will expire in 5 minutes.
                </p>

            </div>
        </div>
        ";

        $mail->AltBody = "OTP: $otp";

        return $mail->send();

    } catch (Exception $e) {
        return false;
    }
}