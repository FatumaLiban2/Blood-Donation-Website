<?php

require_once __DIR__ . "/../autoload.php";

Class SessionManager {
    private const SESSION_COOKIE_NAME = "auth_token";
    private const REFRESH_TOKEN_COOKIE_NAME ='refresh_token';
    private const COOKIE_LIFETIME =3600; //1 hour
    private const REFRESH_TOKEN_LIFETIME = 604800; //7days

    public static function startSession(int $patient_id, string $email, bool $rememberMe = true): array {

        $accessToken = JWT::generateToken($patient_id, $email);

        $cookieLifetime = $rememberMe ? self::REFRESH_TOKEN_LIFETIME : self::COOKIE_LIFETIME;

        self::setSecureCookie(self::SESSION_COOKIE_NAME, $accessToken, $cookieLifetime);

        if ($rememberMe) {
            $refreshToken = self::generateRefreshToken($patient_id, $email);
            self::setSecureCookie(self::REFRESH_TOKEN_COOKIE_NAME, $refreshToken, self::REFRESH_TOKEN_LIFETIME);
        }
        return [
            'success' => true,
            'access_token' => $accessToken,
            'expires_in' => self::COOKIE_LIFETIME
        ];
    }

    //validating the user current session
    public static function validateSession(): ?object {
        // Try to get token from cookie first
        $token = self::getTokenFromCookie();

        // If no cookie, try Authorization header
        if (!$token) {
            $token = self::getTokenFromHeader();
        }

        if (!$token) {
            return null;
        }

        //verify token
        $decoded =JWT::verifyToken($token);

        if (!$decoded){
            //Token invalid, try to refresh if refresh token exists
            return self::attemptTokenRefresh();
        }

        return $decoded;

    }

    // Get current patient ID from session
    public static function getCurrentPatientId(): ?int {
        $session = self::validateSession();
        return $session ? $session->patient_id : null;
    }

    // Get current patient email from session
    public static function getCurrentEmail(): ?string {
        $session = self::validateSession();
        return $session ? $session->email : null;
    }

    // Check if user is authenticated
    public static function isAuthenticated(): bool {
        return self::validateSession() !== null;
    }

    // Require authentication - redirect if not authenticated
    public static function requireAuth(string $redirectUrl = '/login.php'): void {
        if (!self::isAuthenticated()) {
            header("Location: $redirectUrl");
            exit();
        }
    }

    // Refresh session token
    public static function refreshSession(): ?array {
        $refreshToken = $_COOKIE[self::REFRESH_TOKEN_COOKIE_NAME] ?? null;

        if (!$refreshToken) {
            return null;
        }

        $decoded = JWT::verifyToken($refreshToken);

        if (!$decoded || !isset($decoded->patient_id, $decoded->email)) {
            return null;
        }

        // Generate new access token
        return self::startSession($decoded->patient_id, $decoded->email, true);
    }

    // End current session
    public static function endSession(): bool {
        // Delete cookies
        self::deleteCookie(self::SESSION_COOKIE_NAME);
        self::deleteCookie(self::REFRESH_TOKEN_COOKIE_NAME);
        
        // In production, you might want to blacklist the tokens in database/Redis
        
        return true;
    }
    
    // Update session with new data (e.g., after profile update)
    public static function updateSession(int $patient_id, string $email): ?array {
        $currentSession = self::validateSession();
        
        if (!$currentSession) {
            return null;
        }
        
        // Check if remember me was enabled
        $rememberMe = isset($_COOKIE[self::REFRESH_TOKEN_COOKIE_NAME]);
        
        return self::startSession($patient_id, $email, $rememberMe);
    }

    // Get session data as array
    public static function getSessionData(): ?array {
        $session = self::validateSession();
        
        if (!$session) {
            return null;
        }

        return [
            'patient_id' => $session->patient_id,
            'email' => $session->email,
            'issued_at' => $session->iat ?? null,
            'expires_at' => $session->exp ?? null
        ];
    }

    // Check if session is about to expire (within 5 minutes)
    public static function isSessionExpiringSoon(): bool {
        $session = self::validateSession();
        
        if (!$session || !isset($session->exp)) {
            return false;
        }
        
        $timeRemaining = $session->exp - time();

        return $timeRemaining > 0 && $timeRemaining < 300; // 5 minutes
    }

    // PRIVATE HELPER METHODS

    // Generate refresh token with longer expiration
    private static function generateRefreshToken(int $patient_id, string $email): string {
        // You might want to add a 'type' => 'refresh' to the payload
        // For now, using the same JWT generation
        return JWT::generateToken($patient_id, $email);
    }
    
    // Attempt to refresh token automatically
    private static function attemptTokenRefresh(): ?object {
        $refreshToken = $_COOKIE[self::REFRESH_TOKEN_COOKIE_NAME] ?? null;
        
        if (!$refreshToken) {
            return null;
        }
        
        $decoded = JWT::verifyToken($refreshToken);
        
        if (!$decoded) {
            return null;
        }
        
        // Generate new access token
        $newToken = JWT::generateToken($decoded->patient_id, $decoded->email);
        self::setSecureCookie(self::SESSION_COOKIE_NAME, $newToken, self::COOKIE_LIFETIME);
        
        return $decoded;
    }


    private static function getTokenFromCookie(): ?string {
        return $_COOKIE[self::SESSION_COOKIE_NAME] ?? null;
    }

    // Get token from Authorization header
    private static function getTokenFromHeader(): ?string {
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s+(.+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
    
    // Set secure HTTP-only cookie
    private static function setSecureCookie(string $name, string $value, int $lifetime): void {
        $options = [
            'expires' => time() + $lifetime,
            'path' => '/',
            'domain' => 'localhost',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Only send over HTTPS
            'httponly' => true, // Not accessible via JavaScript
            'samesite' => 'Strict' // CSRF protection
        ];
        
        setcookie($name, $value, $options);
    }
    
    // Delete cookie
    private static function deleteCookie(string $name): void {
        if (isset($_COOKIE[$name])) {
            setcookie($name, '', time() - 3600, '/');
            unset($_COOKIE[$name]);
        }
    }
}