<?php

require_once "../autoload.php";

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT {
    private const ALGORITHM = "EdDSA";

    private static function getPrivateKey(): string {
        return base64_decode(JWT_PRIVATE_KEY_ENCODED);
    }

    private static function getPublicKey(): string {
        return base64_decode(JWT_PUBLIC_KEY_ENCODED);
    }

    public static function generateToken($patient_id, $email): string {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // Token valid for 1 hour

        $payload = [
            'patient_id' => $patient_id,
            'email' => $email,
            'iat' => $issuedAt,
            'exp' => $expirationTime
        ];

        $jwt = FirebaseJWT::encode($payload, self::getPrivateKey(), self::ALGORITHM);
        return $jwt;
    }

    public static function verifyToken(string $token) {
        try {
            $decoded = FirebaseJWT::decode($token, new Key(self::getPublicKey(), self::ALGORITHM));
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
