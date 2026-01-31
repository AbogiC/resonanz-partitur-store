<?php
require_once 'models/User.php';
require_once 'models/Profile.php';

class ProfileController
{

    /**
     * Get user profile
     */
    public function getProfile($user_id)
    {
        try {
            // Get user data
            $user = new User();
            $query = "SELECT id, name, email, first_name, last_name FROM users WHERE id = :id LIMIT 1";
            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(":id", $user_id);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                http_response_code(404);
                return json_encode(array("message" => "User not found."));
            }

            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get profile data
            $profile = new Profile();
            $profileData = array(
                "billing_address" => array(
                    "street" => "",
                    "city" => "",
                    "state" => "",
                    "postal_code" => "",
                    "country" => "",
                    "phone" => ""
                ),
                "shipping_address" => array(
                    "street" => "",
                    "city" => "",
                    "state" => "",
                    "postal_code" => "",
                    "country" => "",
                    "phone" => ""
                )
            );

            if ($profile->getByUserId($user_id)) {
                $profileData = $profile->toArray();
            }

            // Combine user and profile data
            $response = array(
                "id" => $userData['id'],
                "name" => $userData['name'] ?: ($userData['first_name'] . ' ' . $userData['last_name']),
                "email" => $userData['email'],
                "billing_address" => $profileData['billing_address'],
                "shipping_address" => $profileData['shipping_address']
            );

            http_response_code(200);
            return json_encode($response);

        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(array(
                "message" => "Failed to get profile.",
                "error" => $e->getMessage()
            ));
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile($user_id, $data)
    {
        try {
            // Validate required fields
            if (empty($data['name']) || empty($data['email'])) {
                http_response_code(400);
                return json_encode(array("message" => "Name and email are required."));
            }

            // Update user basic info
            $database = new Database();
            $conn = $database->getConnection();

            // Check if email is already taken by another user
            $checkQuery = "SELECT id FROM users WHERE email = :email AND id != :user_id LIMIT 1";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(":email", $data['email']);
            $checkStmt->bindParam(":user_id", $user_id);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                http_response_code(400);
                return json_encode(array("message" => "Email is already taken."));
            }

            // Update user name and email
            $userQuery = "UPDATE users SET name = :name, email = :email WHERE id = :id";
            $userStmt = $conn->prepare($userQuery);
            $userStmt->bindParam(":name", $data['name']);
            $userStmt->bindParam(":email", $data['email']);
            $userStmt->bindParam(":id", $user_id);

            if (!$userStmt->execute()) {
                http_response_code(500);
                return json_encode(array("message" => "Failed to update user information."));
            }

            // Update or create profile with addresses
            $profile = new Profile();
            $profile->user_id = $user_id;

            // Set billing address
            if (isset($data['billing_address'])) {
                $profile->billing_street = $data['billing_address']['street'] ?? '';
                $profile->billing_city = $data['billing_address']['city'] ?? '';
                $profile->billing_state = $data['billing_address']['state'] ?? '';
                $profile->billing_postal_code = $data['billing_address']['postal_code'] ?? '';
                $profile->billing_country = $data['billing_address']['country'] ?? '';
                $profile->billing_phone = $data['billing_address']['phone'] ?? '';
            }

            // Set shipping address
            if (isset($data['shipping_address'])) {
                $profile->shipping_street = $data['shipping_address']['street'] ?? '';
                $profile->shipping_city = $data['shipping_address']['city'] ?? '';
                $profile->shipping_state = $data['shipping_address']['state'] ?? '';
                $profile->shipping_postal_code = $data['shipping_address']['postal_code'] ?? '';
                $profile->shipping_country = $data['shipping_address']['country'] ?? '';
                $profile->shipping_phone = $data['shipping_address']['phone'] ?? '';
            }

            if (!$profile->createOrUpdate()) {
                http_response_code(500);
                return json_encode(array("message" => "Failed to update profile addresses."));
            }

            // Get updated profile data
            $profile->getByUserId($user_id);
            $profileData = $profile->toArray();

            // Return updated user data
            $response = array(
                "message" => "Profile updated successfully.",
                "user" => array(
                    "id" => $user_id,
                    "name" => $data['name'],
                    "email" => $data['email'],
                    "billing_address" => $profileData['billing_address'],
                    "shipping_address" => $profileData['shipping_address']
                )
            );

            http_response_code(200);
            return json_encode($response);

        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(array(
                "message" => "Failed to update profile.",
                "error" => $e->getMessage()
            ));
        }
    }
}
?>