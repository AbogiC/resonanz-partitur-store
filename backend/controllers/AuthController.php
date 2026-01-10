<?php
require_once 'models/User.php';
require_once 'middleware/AuthMiddleware.php';

class AuthController {
    
    public function register($data) {
        $user = new User();
        
        $user->email = $data['email'] ?? '';
        $user->password = $data['password'] ?? '';
        $user->first_name = $data['first_name'] ?? '';
        $user->last_name = $data['last_name'] ?? '';
        
        if($user->emailExists()) {
            http_response_code(400);
            return json_encode(array("message" => "Email already exists."));
        }
        
        if($user->create()) {
            $token = AuthMiddleware::generateToken($user->id, $user->email);
            return json_encode(array(
                "message" => "User registered successfully.",
                "token" => $token,
                "user" => array(
                    "id" => $user->id,
                    "email" => $user->email,
                    "first_name" => $user->first_name,
                    "last_name" => $user->last_name
                )
            ));
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Unable to register user."));
        }
    }
    
    public function login($data) {
        $user = new User();
        
        $user->email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        
        if($user->emailExists()) {
            if(password_verify($password, $user->password)) {
                $token = AuthMiddleware::generateToken($user->id, $user->email);
                return json_encode(array(
                    "message" => "Login successful.",
                    "token" => $token,
                    "user" => array(
                        "id" => $user->id,
                        "email" => $user->email,
                        "first_name" => $user->first_name,
                        "last_name" => $user->last_name
                    )
                ));
            }
        }
        
        http_response_code(401);
        return json_encode(array("message" => "Invalid email or password."));
    }
}
?>