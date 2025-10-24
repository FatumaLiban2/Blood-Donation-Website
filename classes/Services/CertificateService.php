<?php
// classes/Services/CertificateService.php
declare(strict_types=1);

/**
 * Robust autoload:
 * 1) Honor composer.json vendor-dir if set
 * 2) Try common Composer locations (root/vendor, resources/vendor)
 * 3) Fallback to manual Dompdf (libs/dompdf)
 * 4) Verify Dompdf is available
 *
 * Do NOT echo/print here; any output can break PDF streaming.
 */
$autoloaded = false;

// 1) Honor composer.json config.vendor-dir if present
$composerJson = __DIR__ . '/../../composer.json';
if (is_file($composerJson)) {
    $json = json_decode(@file_get_contents($composerJson), true);
    if (is_array($json) && !empty($json['config']['vendor-dir'])) {
        $vendorDir = trim($json['config']['vendor-dir'], '/\\');
        $candidate = __DIR__ . '/../../' . $vendorDir . '/autoload.php';
        if (is_file($candidate)) {
            require_once $candidate;
            $autoloaded = true;
        }
    }
}

// 2) Try common Composer autoload locations
if (!$autoloaded) {
    $candidates = [
        __DIR__ . '/../../vendor/autoload.php',            // project root
        __DIR__ . '/../../resources/vendor/autoload.php',  // vendor under resources/
        __DIR__ . '/../../../vendor/autoload.php',         // safety fallback
    ];
    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            require_once $candidate;
            $autoloaded = true;
            break;
        }
    }
}

// 3) Manual Dompdf fallback (no Composer)
if (!$autoloaded || !class_exists('Dompdf\\Dompdf')) {
    $dompdfAutoload = __DIR__ . '/../../libs/dompdf/autoload.inc.php';
    if (is_file($dompdfAutoload)) {
        require_once $dompdfAutoload;
        $autoloaded = true;
    }
}

// 4) Verify Dompdf is available
if (!class_exists('Dompdf\\Dompdf')) {
    throw new RuntimeException(
        'Dompdf autoload failed. Install via Composer (composer require dompdf/dompdf) ' .
        'or place Dompdf at libs/dompdf (with autoload.inc.php).'
    );
}

// Now safe to import
use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateService
{
    // Branding (customize)
    private string $hospitalName     = 'LifeSavers Medical Center';
    private string $departmentName   = 'Blood Donation Center';
    private string $hospitalAddress  = '123 Health Avenue, Cityville, State 12345';
    private string $hospitalWebsite  = 'https://www.lifesavershospital.org';

    // Theme colors
    private string $primaryColor     = '#B22234'; // blood red
    private string $goldColor        = '#d4af37'; // gold accent

    // Images: prefer local embed (reliable), fallback to remote URL if provided
    private ?string $logoLocalPath   = __DIR__ . '/../../resources/images/logo.png';
    private ?string $logoUrl         = ''; // e.g. 'https://your-domain.com/resources/images/logo.png'
    // Optional watermark (semi-transparent PNG)
    private ?string $watermarkLocal  = __DIR__ . '/../../resources/images/watermark.png';

    // Allow https images (for remote QR or remote logo)
    private bool $enableRemote = true;

    /**
     * Generate and force-download the PDF certificate.
     * Required in $data: name, date (Y-m-d), blood_type, units, location, certificate_id
     * Optional: verify_url (QR), title (override)
     */
    public function downloadDonationCertificate(array $data): void
    {
        $dompdf = $this->buildDompdf();
        $html   = $this->buildCertificateHtml($data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Donation_Certificate_' . ($data['certificate_id'] ?? date('Ymd')) . '.pdf';
        // Important: no output before streaming
        $dompdf->stream($filename, ['Attachment' => true]); // true = download
        exit;
    }

    /**
     * Render and return PDF bytes (for email attachments).
     */
    public function renderDonationCertificate(array $data): string
    {
        $dompdf = $this->buildDompdf();
        $html   = $this->buildCertificateHtml($data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }

    private function buildDompdf(): Dompdf
    {
        $options = new Options();
        $options->set('isRemoteEnabled', $this->enableRemote); // needed for https QR/remote images
        $options->set('defaultFont', 'DejaVu Sans');           // good glyph support
        // If you hit temp/permission issues on Windows:
        // $options->set('tempDir', __DIR__ . '/../../storage/tmp');
        return new Dompdf($options);
    }

    /**
     * Updated styling with extra right padding and a classy inner panel.
     */
    private function buildCertificateHtml(array $data): string
    {
        // Logo: embed local if available, else use remote URL
        $logoTag = '';
        if (!empty($this->logoLocalPath) && file_exists($this->logoLocalPath)) {
            $logoTag = "<img src=\"" . $this->imageToDataUri($this->logoLocalPath) . "\" alt=\"Logo\" style=\"height:72px;\">";
        } elseif (!empty($this->logoUrl)) {
            $logoTag = "<img src=\"{$this->eUrl($this->logoUrl)}\" alt=\"Logo\" style=\"height:72px;\">";
        }

        // Optional watermark
        $watermarkCss = '';
        if (!empty($this->watermarkLocal) && file_exists($this->watermarkLocal)) {
            $wmPath = 'file://' . realpath($this->watermarkLocal);
            $watermarkCss = "background: url('{$this->eUrl($wmPath)}') no-repeat center center; background-size: 60% auto;";
        }

        // Optional verification QR
        $qrHtml = '';
        if (!empty($data['verify_url'])) {
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' . rawurlencode((string)$data['verify_url']);
            $qrHtml = "<div style='text-align:right;'>
                         <img src='{$this->eUrl($qrUrl)}' alt='QR' style='width:120px;height:120px;'>
                         <div style='font-size:10px;color:#666;margin-top:6px;'>Scan to verify</div>
                       </div>";
        }

        $title = $data['title'] ?? 'Certificate of Appreciation';
        $name  = $data['name']  ?? 'Valued Donor';

        return "
<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <style>
    @page { margin: 0; }
    body { margin:0; padding:0; font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color:#333; }

    /* Increased L/R padding (more space on right) */
    .page {
      width: 297mm; height: 210mm; /* A4 landscape */
      padding: 20mm 28mm; /* top/bottom 20mm, left/right 28mm */
      $watermarkCss
      box-sizing: border-box;
      background: #ffffff;
    }

    /* Outer gold frame */
    .frame {
      width: 100%; height: 100%;
      border: 8px double {$this->goldColor};
      box-sizing: border-box;
      padding: 16mm 22mm; /* extra right padding */
      position: relative;
      background: #fff;
    }

    /* Inner panel to make it stand out */
    .inner {
      max-width: 250mm; /* keeps content centered, away from edges */
      margin: 0 auto;
      border: 4px double #e7cf80;
      background: #fffdf5; /* soft off-white */
      border-radius: 6px;
      padding: 12mm 16mm; /* generous inner padding */
      box-sizing: border-box;
    }

    .header { display:flex; align-items:center; justify-content:space-between; margin-bottom:8mm; }
    .brand-left { display:flex; align-items:center; gap:10px; }
    .brand-text { line-height:1.2; }
    .brand-title { font-size:18px; font-weight:700; color: {$this->primaryColor}; }
    .brand-sub   { font-size:12px; color:#666; }

    .title {
      text-align:center; font-size:34px; font-weight:800; color: {$this->goldColor};
      letter-spacing:1px; text-transform:uppercase; margin:6mm 0 2mm 0;
    }
    .subtitle { text-align:center; font-size:14px; color:#666; margin-bottom:10mm; }

    .recipient { text-align:center; margin:0 0 8mm 0; }
    .recipient .label { font-size:12px; letter-spacing:2px; color:#888; text-transform:uppercase; }
    .recipient .name  { font-size:32px; font-weight:700; color:#222; margin-top:3mm; }

    .citation { text-align:center; font-size:14px; color:#444; margin-bottom:10mm; }

    .panel {
      border: 1px solid #e5e5e5; background: #fafafa;
      border-left: 4px solid {$this->primaryColor};
      padding: 6mm 8mm; margin-bottom: 10mm;
    }
    .panel h3 { margin:0 0 4mm 0; color: {$this->primaryColor}; font-size:16px; }

    .grid { display:flex; flex-wrap:wrap; gap:10mm; font-size:13px; color:#333; }
    .grid .item { min-width: 60mm; }
    .hl { color:#111; font-weight:700; }

    .footer {
      display:flex; justify-content:space-between; align-items:flex-end;
      gap: 10mm; /* breathing space on the right */
    }
    .signatures { display:flex; gap:18mm; }
    .sig-block { width:70mm; text-align:center; border-top:1px solid #444; padding-top:4mm; font-size:12px; color:#555; }
    .meta { text-align:right; font-size:11px; color:#777; }

    .ribbon {
      position: absolute; top: -8px; left: -8px;
      border-top: 28px solid {$this->primaryColor};
      border-right: 28px solid transparent; width: 0;
    }
  </style>
</head>
<body>
  <div class='page'>
    <div class='frame'>
      <div class='ribbon'></div>

      <div class='inner'>
        <div class='header'>
          <div class='brand-left'>
            <div class='logo'>$logoTag</div>
            <div class='brand-text'>
              <div class='brand-title'>{$this->e($this->hospitalName)}</div>
              <div class='brand-sub'>{$this->e($this->departmentName)}</div>
            </div>
          </div>
          $qrHtml
        </div>

        <div class='title'>{$this->e($title)}</div>
        <div class='subtitle'>In grateful recognition of outstanding generosity and life‑saving impact</div>

        <div class='recipient'>
          <div class='label'>Presented to</div>
          <div class='name'>{$this->e($name)}</div>
        </div>

        <div class='citation'>
          Thank you for your life‑saving blood donation. Your compassion and commitment help patients
          and families in our community every day.
        </div>

        <div class='panel'>
          <h3>Donation Details</h3>
          <div class='grid'>
            <div class='item'><span class='hl'>Date:</span> {$this->e($data['date'] ?? '')}</div>
            <div class='item'><span class='hl'>Blood Type:</span> {$this->e($data['blood_type'] ?? '')}</div>
            <div class='item'><span class='hl'>Units Donated:</span> {$this->e((string)($data['units'] ?? ''))}</div>
            <div class='item'><span class='hl'>Donation Center:</span> {$this->e($data['location'] ?? '')}</div>
            <div class='item'><span class='hl'>Certificate ID:</span> {$this->e($data['certificate_id'] ?? '')}</div>
          </div>
        </div>

        <div class='footer'>
          <div class='signatures'>
            <div class='sig-block'>Medical Director<br>{$this->e($this->hospitalName)}</div>
            <div class='sig-block'>Donation Coordinator<br>{$this->e($this->departmentName)}</div>
          </div>
          <div class='meta'>
            {$this->e($this->hospitalAddress)}<br>
            {$this->e($this->hospitalWebsite)}
          </div>
        </div>
      </div> <!-- /inner -->

    </div>
  </div>
</body>
</html>
";
    }

    /**
     * Embed a local image as a data URI (reliable in PDFs).
     */
    private function imageToDataUri(string $path): string
    {
        $real = realpath($path);
        if (!$real || !is_readable($real)) {
            return '';
        }
        $mime = function_exists('mime_content_type') ? (string)mime_content_type($real) : 'image/png';
        $data = @file_get_contents($real);
        if ($data === false) {
            return '';
        }
        return 'data:' . $this->eUrl($mime) . ';base64,' . base64_encode($data);
    }

    // Escapers
    private function e(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function eUrl(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

