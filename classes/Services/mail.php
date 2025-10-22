<?php

namespace Services;

require_once "../../autoload.php";
require_once "../../config/config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mail {
    private $mail;
    private $recipient_email;


    public function __construct($recipient_email) {
        $this->mail = new PHPMailer(true);
        $this->recipient_email = $recipient_email;

        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = SMTP_HOST;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = SMTP_USER;
        $this->mail->Password = SMTP_PASSWORD;
        $this->mail->SMTPSecure = SMTP_ENCRYPTION;
        $this->mail->Port = SMTP_PORT;

        // Sender info
        $this->mail->setFrom(SMTP_USER, 'Blood Life Donation');
    }
    
    public function sendOTPMail($recipient_email, $name, $code) {
        try {
            // Recipient Info
            $this->mail->addAddress($recipient_email, $name);

            // Email content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Blood Life Donation - OTP Verification Code';
            $this->mail->Body = $this->getOtpEmailTemplate($name, $code);
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
            return false;
        }
    }
    
    public function getOtpEmailTemplate($name, $code) {
        return "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verify Your Email</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header .tagline {
            color: #fecaca;
            margin: 8px 0 0 0;
            font-size: 14px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .otp-container {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px dashed #dc2626;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-label {
            font-size: 14px;
            color: #991b1b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 700;
            color: #dc2626;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .expiry {
            font-size: 13px;
            color: #991b1b;
            margin-top: 12px;
        }
        .warning-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 0;
            font-size: 14px;
            color: #92400e;
            line-height: 1.5;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 8px 0;
            font-size: 13px;
            color: #6b7280;
        }
        .footer a {
            color: #dc2626;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            .otp-code {
                font-size: 32px;
                letter-spacing: 6px;
            }
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <!-- Header -->
        <div class='header'>
            <h1>Life Blood</h1>
            <p class='tagline'>Save Lives Through Blood Donations</p>
        </div>

        <!-- Content -->
        <div class='content'>
            <p class='greeting'>Hello {$name},</p>
            
            <p class='message'>
                Thank you for registering with Life Blood. To complete your registration and verify your email address, please use the One-Time Password (OTP) below:
            </p>

            <!-- OTP Box -->
            <div class='otp-container'>
                <div class='otp-label'>Your Verification Code</div>
                <div class='otp-code'>{$code}</div>
                <div class='expiry'>⏰ This code expires in 10 minutes</div>
            </div>

            <p class='message'>
                Enter this code on the verification page to continue. If you didn't request this code, please ignore this email or contact our support team.
            </p>

            <!-- Warning Box -->
            <div class='warning-box'>
                <p>
                    <strong>🔒 Security Reminder:</strong> Never share this code with anyone. Our team will never ask you for this code via phone, email, or text message.
                </p>
            </div>

            <p class='message'>
                Thank you for joining Life Blood. Your donation can save up to three lives. Every donation makes a difference!
            </p>
        </div>

        <!-- Footer -->
        <div class='footer'>
            <p><strong>Life Blood</strong></p>
            <p>Questions? Contact us at <a href='mailto:support@lifeblood.org'>support@lifeblood.org</a></p>
            <p style='margin-top: 20px; font-size: 12px;'>
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
    ";
    }
}
