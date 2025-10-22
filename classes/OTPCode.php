<?php
declare(strict_types=1);

class OTPCode {

    // Function to generate a 6 digit OTP-CODE
    public static function generate(): string {
        $num = random_int(0, 999999);
        // Add padding to the left side for non-six digit number
        return str_pad((string)$num, 6, '0', STR_PAD_LEFT);
    }
}
