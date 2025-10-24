<?php
// download_certificate.php (root)
// Streams a downloadable PDF certificate using your robust CertificateService.
// No output before streaming!

declare(strict_types=1);

require_once __DIR__ . '/classes/Services/CertificateService.php';

// Small helpers (no output)
function req(string $key, $default = null) {
    return isset($_REQUEST[$key]) && $_REQUEST[$key] !== ''
        ? $_REQUEST[$key]
        : $default;
}

// Collect and sanitize inputs (GET or POST)
$name          = trim((string) req('name', 'Test User'));
$date          = (string) req('date', date('Y-m-d'));
$blood         = strtoupper(trim((string) req('blood_type', 'O+')));
$units         = (int) req('units', 1);
$location      = trim((string) req('location', 'LifeSavers Hospital Donation Center'));
$certificateId = (string) req('certificate_id', 'CERT-' . date('Ymd') . '-' . rand(1000, 9999));
$verifyUrl     = (string) req('verify_url', 'https://www.lifesavershospital.org/verify?cert=' . urlencode($certificateId));

// Basic guards (avoid absurd values)
if ($units < 0)  $units = 0;
if ($units > 10) $units = 10; // sanity cap for display

// Build payload for the certificate
$data = [
    'name'           => $name,
    'date'           => $date,
    'blood_type'     => $blood,
    'units'          => $units,
    'location'       => $location,
    'certificate_id' => $certificateId,
    'verify_url'     => $verifyUrl,
    // 'title'        => 'Certificate of Appreciation', // optional override
];

$svc = new CertificateService();

// Generate and download the PDF (Dompdf handles headers)
$svc->downloadDonationCertificate($data);

// No closing PHP tag (prevents accidental output)
