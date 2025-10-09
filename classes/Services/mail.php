<?php
// Include necessary files
require_once 'autoload.php';
require_once 'config/config.php';

// Use PHPMailer if you have it installed (recommended)
// If not, we can use PHP's mail() function

class EmailService {
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        // Set default sender details
        $this->fromEmail = 'noreply@blooddonation.com'; // Replace with your actual email
        $this->fromName = 'Blood Donation Service';
    }
    
    /**
     * Send email using PHP's mail function
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email body
     * @param array $headers Additional headers
     * @return bool Success or failure
     */
    // Add any additional headers
        $headers = array_merge($headers, $additionalHeaders);
        
        // Send email
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
    
    /**
     * Send donation confirmation email
     * 
     * @param string $to Recipient email
     * @param array $donationDetails Details about the donation
     * @return bool Success or failure
     */
    public function sendDonationConfirmation($to, $donationDetails) {
        $subject = 'Thank You for Your Blood Donation';
        
        // Create HTML message
        $message = "
        <html>
        <head>
            <title>Thank You for Your Blood Donation</title>
        </head>
        <body>
            <h2>Thank You for Your Blood Donation!</h2>
            <p>Dear {$donationDetails['name']},</p>
            <p>Thank you for your recent blood donation. Your generosity helps save lives!</p>
            <p><strong>Donation Details:</strong></p>
            <ul>
                <li>Date: {$donationDetails['date']}</li>
                <li>Blood Type: {$donationDetails['blood_type']}</li>
                <li>Location: {$donationDetails['location']}</li>
            </ul>
            <p>Your next eligible donation date will be: {$donationDetails['next_date']}</p>
            <p>Thank you again for your life-saving gift!</p>
            <p>Sincerely,<br>The Blood Donation Team</p>
        </body>
        </html>
        ";
        
        return $this->sendEmail($to, $subject, $message);
    }
    