<?php
require_once 'config/constants.php';

class AuthMiddleware {
    
    public static function authenticate($required = true) {
        $headers = getallheaders();
        
        if(isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            
            if(self::validateToken($token)) {
                return true;
            }
        }
        
        if($required) {
            http_response_code(401);
            echo json_encode(array("message" => "Access denied. Authentication required."));
            exit;
        }
        
        return false;
    }
    
    private static function validateToken($token) {
        // In a real application, use JWT or similar
        // This is a simplified version
        $secret = SECRET_KEY;
        
        // Decode token (simplified - use proper JWT library in production)
        $parts = explode('.', $token);
        if(count($parts) === 3) {
            $payload = json_decode(base64_decode($parts[1]), true);
            
            if(isset($payload['user_id']) && isset($payload['exp'])) {
                if($payload['exp'] > time()) {
                    $_SESSION['user_id'] = $payload['user_id'];
                    $_SESSION['user_email'] = $payload['email'];
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public static function generateToken($user_id, $email) {
        $payload = [
            'user_id' => $user_id,
            'email' => $email,
            'exp' => time() + (24 * 60 * 60) // 24 hours
        ];
        
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payload);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, SECRET_KEY, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}
?>