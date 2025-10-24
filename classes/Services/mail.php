<?php
// classes/Services/mail.php
// Using PHPMailer from a local clone (no Composer autoload)

// Manually include PHPMailer classes from libs/PHPMailer
require_once __DIR__ . '/../../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../libs/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../../libs/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class EmailService
{
    // Branding / content (update to your hospital)
    private string $fromName        = 'LifeSavers Hospital Blood Donation Center';
    private string $hospitalName    = 'LifeSavers Medical Center';
    // Use a public URL for the logo (CID embed can be added if you prefer)
    private string $hospitalLogo    = 'https://your-domain.com/resources/images/logo.png';
    private string $hospitalAddress = '123 Health Avenue, Cityville, State 12345';
    private string $hospitalPhone   = '(555) 123-4567';
    private string $hospitalWebsite = 'https://www.lifesavershospital.org';
    private string $primaryColor    = '#B22234'; // blood red

    // SMTP settings (REPLACE with your sender account)
    private string $smtpHost       = 'smtp.gmail.com';
    private string $smtpUsername   = 'jamal.kiki@strathmore.edu';   // e.g. jamal.kiki@strathmore.edu
    private string $smtpPassword   = 'ulkqvvwyylgdasjs';      // Gmail App Password (remove spaces)
    private int    $smtpPort       = 587;
    private string $smtpSecure     = PHPMailer::ENCRYPTION_STARTTLS; // or PHPMailer::ENCRYPTION_SMTPS with port 465

    // Diagnostics
    private bool $debugSmtp = false;
    private ?string $lastError = null;

    // Optional: runtime overrides
    public function setDebug(bool $on): void { $this->debugSmtp = $on; }
    public function getLastError(): ?string { return $this->lastError; }
    public function setSmtp(string $host, string $user, string $pass, int $port = 587, string $secure = PHPMailer::ENCRYPTION_STARTTLS): void
    {
        $this->smtpHost = $host;
        $this->smtpUsername = $user;
        $this->smtpPassword = $pass;
        $this->smtpPort = $port;
        $this->smtpSecure = $secure;
    }

    // Core SMTP sender (fresh instance each call; explicitly closes socket)
    public function sendEmail(string $to, string $subject, string $html, array $extraHeaders = []): bool
    {
        $this->lastError = null;
        $to = trim($to);

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->lastError = "Invalid recipient email: $to";
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            if ($this->debugSmtp) {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER; // verbose
            }

            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUsername;
            $mail->Password   = $this->smtpPassword; // App Password (no spaces)
            $mail->SMTPSecure = $this->smtpSecure;
            $mail->Port       = $this->smtpPort;
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 20;

            // From must align with authenticated account/domain
            $mail->setFrom($this->smtpUsername, $this->fromName);
            $mail->addAddress($to);

            foreach ($extraHeaders as $hName => $hValue) {
                $mail->addCustomHeader($hName, $hValue);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $this->toAltBody($html);

            $mail->send();
            if (method_exists($mail, 'smtpClose')) {
                $mail->smtpClose(); // prevents second-send failures on Windows
            }
            return true;
        } catch (Exception $e) {
            $this->lastError = $mail->ErrorInfo ?: $e->getMessage();
            if (method_exists($mail, 'smtpClose')) {
                $mail->smtpClose();
            }
            return false;
        }
    }

    private function toAltBody(string $html): string
    {
        // Simple HTML->text fallback
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = strip_tags($text);
        return html_entity_decode($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function esc(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function getEmailHeader(): string
    {
        // Styled header + content container (same styling you liked)
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$this->hospitalName} - Blood Donation</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background-color: {$this->primaryColor}; padding: 20px; text-align: center; }
                .logo { max-height: 80px; }
                .content { padding: 20px; }
                .footer { background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color:#555; }
                h2 { color: {$this->primaryColor}; }
                .button { background-color: {$this->primaryColor}; color: white; padding: 10px 20px;
                          text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
                .details { background-color: #f9f9f9; padding: 15px; border-left: 4px solid {$this->primaryColor}; }
            </style>
        </head>
        <body>
            <div class='header'>
                <img src='{$this->hospitalLogo}' alt='{$this->hospitalName} Logo' class='logo'>
            </div>
            <div class='content'>
        ";
    }

    private function getEmailFooter(): string
    {
        return "
            </div>
            <div class='footer'>
                <p>{$this->hospitalName} Blood Donation Center</p>
                <p>{$this->hospitalAddress}</p>
                <p>Phone: {$this->hospitalPhone} | Website: {$this->hospitalWebsite}</p>
                <p>This email was sent to you because you are registered with our blood donation program.</p>
            </div>
        </body>
        </html>
        ";
    }

    // Donation confirmation (keeps the original styling/content)
    public function sendDonationConfirmation(string $to, array $donationDetails): bool
    {
        $subject = 'Thank You for Your Blood Donation at ' . $this->hospitalName;

        $message = $this->getEmailHeader();
        $message .= "
            <h2>Thank You for Your Life-Saving Donation!</h2>
            <p>Dear {$this->esc($donationDetails['name'] ?? 'Donor')},</p>
            <p>On behalf of the entire {$this->hospitalName} team and the patients whose lives you've helped save,
               we sincerely thank you for your recent blood donation.</p>

            <div class='details'>
                <h3>Your Donation Details:</h3>
                <p><strong>Date:</strong> {$this->esc($donationDetails['date'] ?? '')}</p>
                <p><strong>Blood Type:</strong> {$this->esc($donationDetails['blood_type'] ?? '')}</p>
                <p><strong>Donation Center:</strong> {$this->esc($donationDetails['location'] ?? '')}</p>
                <p><strong>Units Donated:</strong> {$this->esc((string)($donationDetails['units'] ?? ''))} unit(s)</p>
                <p><strong>Next Eligible Donation Date:</strong> {$this->esc($donationDetails['next_date'] ?? '')}</p>
            </div>

            <p>Your donation can help up to three patients in need.</p>
            <p>We've added this donation to your donor history. You can view your complete donation
               history and schedule your next appointment by visiting our website.</p>

            <a href='{$this->hospitalWebsite}/donor-portal' class='button'>Visit Donor Portal</a>

            <p>Remember to stay hydrated, eat iron-rich foods, and avoid strenuous activity for the next 24 hours.</p>
            <p>Thank you again for being a hero in our community!</p>
            <p>With gratitude,<br>The {$this->hospitalName} Blood Donation Team</p>
        ";
        $message .= $this->getEmailFooter();

        return $this->sendEmail($to, $subject, $message);
    }

    // Appointment confirmation (keeps the original styling/content)
    public function sendAppointmentConfirmation(string $to, array $appointmentDetails): bool
    {
        $subject = 'Your Blood Donation Appointment at ' . $this->hospitalName;

        $message = $this->getEmailHeader();
        $message .= "
            <h2>Your Blood Donation Appointment is Confirmed</h2>
            <p>Dear {$this->esc($appointmentDetails['name'] ?? 'Donor')},</p>
            <p>Thank you for scheduling a blood donation appointment with {$this->hospitalName}.
               Your commitment to helping others is truly appreciated.</p>

            <div class='details'>
                <h3>Appointment Details:</h3>
                <p><strong>Date:</strong> {$this->esc($appointmentDetails['date'] ?? '')}</p>
                <p><strong>Time:</strong> {$this->esc($appointmentDetails['time'] ?? '')}</p>
                <p><strong>Location:</strong> {$this->esc($appointmentDetails['location'] ?? '')}</p>
                <p><strong>Appointment Type:</strong> {$this->esc($appointmentDetails['type'] ?? '')} Donation</p>
                <p><strong>Appointment ID:</strong> {$this->esc($appointmentDetails['id'] ?? '')}</p>
            </div>

            <p><strong>Please bring:</strong></p>
            <ul>
                <li>A valid photo ID</li>
                <li>List of medications you're currently taking</li>
                <li>Information about recent travel outside the country</li>
            </ul>

            <div class='details'>
                <h3>Preparation Tips:</h3>
                <ul>
                    <li>Get a good night's sleep before your donation</li>
                    <li>Eat a healthy, iron-rich meal within 3 hours of your donation</li>
                    <li>Drink plenty of water before and after donating</li>
                    <li>Avoid caffeine and alcoholic beverages before donating</li>
                    <li>Wear comfortable clothing with sleeves that can be raised above the elbow</li>
                </ul>
            </div>

            <p>Need to reschedule? Please contact us at least 24 hours in advance at {$this->hospitalPhone}
               or reply to this email.</p>

            <a href='{$this->hospitalWebsite}/appointments/{$this->esc($appointmentDetails['id'] ?? '')}' class='button'>Manage Your Appointment</a>

            <p>Thank you for your commitment to saving lives in our community!</p>
            <p>Sincerely,<br>The {$this->hospitalName} Blood Donation Team</p>
        ";
        $message .= $this->getEmailFooter();

        return $this->sendEmail($to, $subject, $message);
    }
}

