<?php
declare(strict_types=1);

/**
 * Simple random activation code generator.
 *
 * Provides a static method to generate a cryptographically secure
 * 6-digit numeric code returned as a zero-padded string.
 */
class RandomCode {
    /**
     * Generate a cryptographically secure 6-digit code.
     *
     * @return string Six-digit numeric code (may contain leading zeros)
     * @throws Exception if a secure random number cannot be generated
     */
    public static function generate(): string {
        // Range 0..999999 then pad to 6 digits to preserve leading zeros
        $num = random_int(0, 999999);
        return str_pad((string)$num, 6, '0', STR_PAD_LEFT);
    }
}
