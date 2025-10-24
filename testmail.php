<?php
// testmail.php (root) — using cloned PHPMailer (no Composer autoloader)
require_once 'classes/Services/mail.php';

function h($v) { return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$postedEmail = $_POST['email'] ?? '';
$postedName  = $_POST['name']  ?? 'Test User';
$postedType  = $_POST['test_type'] ?? 'donation';
$postedDebug = !empty($_POST['debug']);

echo "<h1>Blood Donation Email Service Test</h1>";

echo "<h2>Sender Information:</h2>";
echo "<ul>
        <li><strong>From Email:</strong> Uses the SMTP username configured in EmailService</li>
        <li><strong>From Name:</strong> LifeSavers Hospital Blood Donation Center</li>
      </ul>";

echo "<div style='background:#fff3cd;padding:15px;border-left:4px solid #ffc107;margin-bottom:20px;'>
        <h3 style='margin:0 0 8px 0;'>Important</h3>
        <p>Update SMTP in classes/Services/mail.php:
           host, username (your email), Gmail App Password (no spaces), port 587, TLS.</p>
      </div>";

echo "<h2>Test Options:</h2>";
echo "<form method='post' style='margin-bottom:20px;'>
        <label for='email'>Recipient Email:</label><br>
        <input type='email' id='email' name='email' value='".h($postedEmail)."' required style='width:300px;margin-bottom:10px;'><br>

        <label for='name'>Recipient Name:</label><br>
        <input type='text' id='name' name='name' value='".h($postedName)."' style='width:300px;margin-bottom:10px;'><br>

        <label for='test_type'>Select Email Type:</label><br>
        <select id='test_type' name='test_type' style='width:300px;margin-bottom:10px;'>
            <option value='donation' ".($postedType==='donation'?'selected':'').">Donation Confirmation</option>
            <option value='appointment' ".($postedType==='appointment'?'selected':'').">Appointment Confirmation</option>
        </select><br>

        <label><input type='checkbox' name='debug' ".($postedDebug?'checked':'')."> Enable SMTP debug</label><br><br>

        <input type='submit' value='Send Test Email' style='padding:8px 15px;background:#B22234;color:#fff;border:none;cursor:pointer;'>
      </form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $recipientEmail = trim($_POST['email']);
    $recipientName  = trim($_POST['name'] ?: 'Test User');
    $testType       = $_POST['test_type'] ?? 'donation';

    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;font-weight:bold;'>Invalid email address.</p>";
        exit;
    }

    $svc = new EmailService();
    if ($postedDebug && method_exists($svc, 'setDebug')) {
        $svc->setDebug(true);
    }

    $result = false;
    $emailType = '';

    if ($testType === 'donation') {
        $d = [
            'name'       => $recipientName,
            'date'       => date('Y-m-d'),
            'blood_type' => 'O+',
            'location'   => 'LifeSavers Hospital Donation Center',
            'units'      => 1,
            'next_date'  => date('Y-m-d', strtotime('+56 days')),
        ];
        $result   = $svc->sendDonationConfirmation($recipientEmail, $d);
        $emailType = 'Donation Confirmation';

        echo "<h2>Email Content Preview:</h2>
              <h3>Subject: Thank You for Your Blood Donation</h3>
              <p><strong>Recipient:</strong> ".h($recipientEmail)."</p>
              <p><strong>Name:</strong> ".h($recipientName)."</p>
              <ul>
                <li>Donation date: ".h($d['date'])."</li>
                <li>Blood type: ".h($d['blood_type'])."</li>
                <li>Location: ".h($d['location'])."</li>
                <li>Units: ".h($d['units'])."</li>
                <li>Next eligible date: ".h($d['next_date'])."</li>
              </ul>";

    } else {
        $a = [
            'name'     => $recipientName,
            'date'     => date('Y-m-d', strtotime('+7 days')),
            'time'     => '10:00 AM',
            'location' => 'LifeSavers Hospital Donation Center',
            'type'     => 'Whole Blood',
            'id'       => 'APT-' . rand(10000, 99999),
        ];
        $result   = $svc->sendAppointmentConfirmation($recipientEmail, $a);
        $emailType = 'Appointment Confirmation';

        echo "<h2>Email Content Preview:</h2>
              <h3>Subject: Your Blood Donation Appointment</h3>
              <p><strong>Recipient:</strong> ".h($recipientEmail)."</p>
              <p><strong>Name:</strong> ".h($recipientName)."</p>
              <ul>
                <li>Appointment date: ".h($a['date'])."</li>
                <li>Time: ".h($a['time'])."</li>
                <li>Location: ".h($a['location'])."</li>
                <li>Type: ".h($a['type'])."</li>
                <li>ID: ".h($a['id'])."</li>
              </ul>";
    }

    echo "<h2>Email Sending Result:</h2>";
    if ($result) {
        echo "<p style='color:green;font-weight:bold;'>✓ $emailType email sent successfully to ".h($recipientEmail)."!</p>";
        echo "<p>Please check your inbox (and spam folder).</p>";
    } else {
        echo "<p style='color:red;font-weight:bold;'>✗ Failed to send $emailType email.</p>";
        if (method_exists($svc, 'getLastError')) {
            $err = $svc->getLastError();
            if (!empty($err)) {
                echo "<h3>Error detail</h3>
                      <pre style='white-space:pre-wrap;background:#f8f8f8;border:1px solid #eee;padding:10px;'>".h($err)."</pre>";
            }
        }
        echo "<p>Verify SMTP username, App Password (no spaces), port/security, and that setFrom matches the SMTP account.</p>";
    }
}


