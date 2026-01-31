<?php
require_once 'config/database.php';

class Profile
{
    private $conn;
    private $table_name = "user_profiles";

    public $id;
    public $user_id;
    public $billing_street;
    public $billing_city;
    public $billing_state;
    public $billing_postal_code;
    public $billing_country;
    public $billing_phone;
    public $shipping_street;
    public $shipping_city;
    public $shipping_state;
    public $shipping_postal_code;
    public $shipping_country;
    public $shipping_phone;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get user profile by user_id
     */
    public function getByUserId($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->billing_street = $row['billing_street'];
            $this->billing_city = $row['billing_city'];
            $this->billing_state = $row['billing_state'];
            $this->billing_postal_code = $row['billing_postal_code'];
            $this->billing_country = $row['billing_country'];
            $this->billing_phone = $row['billing_phone'];
            $this->shipping_street = $row['shipping_street'];
            $this->shipping_city = $row['shipping_city'];
            $this->shipping_state = $row['shipping_state'];
            $this->shipping_postal_code = $row['shipping_postal_code'];
            $this->shipping_country = $row['shipping_country'];
            $this->shipping_phone = $row['shipping_phone'];
            return true;
        }
        return false;
    }

    /**
     * Create a new profile
     */
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET user_id = :user_id,
                    billing_street = :billing_street,
                    billing_city = :billing_city,
                    billing_state = :billing_state,
                    billing_postal_code = :billing_postal_code,
                    billing_country = :billing_country,
                    billing_phone = :billing_phone,
                    shipping_street = :shipping_street,
                    shipping_city = :shipping_city,
                    shipping_state = :shipping_state,
                    shipping_postal_code = :shipping_postal_code,
                    shipping_country = :shipping_country,
                    shipping_phone = :shipping_phone";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":billing_street", $this->billing_street);
        $stmt->bindParam(":billing_city", $this->billing_city);
        $stmt->bindParam(":billing_state", $this->billing_state);
        $stmt->bindParam(":billing_postal_code", $this->billing_postal_code);
        $stmt->bindParam(":billing_country", $this->billing_country);
        $stmt->bindParam(":billing_phone", $this->billing_phone);
        $stmt->bindParam(":shipping_street", $this->shipping_street);
        $stmt->bindParam(":shipping_city", $this->shipping_city);
        $stmt->bindParam(":shipping_state", $this->shipping_state);
        $stmt->bindParam(":shipping_postal_code", $this->shipping_postal_code);
        $stmt->bindParam(":shipping_country", $this->shipping_country);
        $stmt->bindParam(":shipping_phone", $this->shipping_phone);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Update profile
     */
    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                SET billing_street = :billing_street,
                    billing_city = :billing_city,
                    billing_state = :billing_state,
                    billing_postal_code = :billing_postal_code,
                    billing_country = :billing_country,
                    billing_phone = :billing_phone,
                    shipping_street = :shipping_street,
                    shipping_city = :shipping_city,
                    shipping_state = :shipping_state,
                    shipping_postal_code = :shipping_postal_code,
                    shipping_country = :shipping_country,
                    shipping_phone = :shipping_phone
                WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":billing_street", $this->billing_street);
        $stmt->bindParam(":billing_city", $this->billing_city);
        $stmt->bindParam(":billing_state", $this->billing_state);
        $stmt->bindParam(":billing_postal_code", $this->billing_postal_code);
        $stmt->bindParam(":billing_country", $this->billing_country);
        $stmt->bindParam(":billing_phone", $this->billing_phone);
        $stmt->bindParam(":shipping_street", $this->shipping_street);
        $stmt->bindParam(":shipping_city", $this->shipping_city);
        $stmt->bindParam(":shipping_state", $this->shipping_state);
        $stmt->bindParam(":shipping_postal_code", $this->shipping_postal_code);
        $stmt->bindParam(":shipping_country", $this->shipping_country);
        $stmt->bindParam(":shipping_phone", $this->shipping_phone);
        $stmt->bindParam(":user_id", $this->user_id);

        return $stmt->execute();
    }

    /**
     * Create or update profile (upsert)
     */
    public function createOrUpdate()
    {
        // Check if profile exists
        if ($this->getByUserId($this->user_id)) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    /**
     * Get profile as array
     */
    public function toArray()
    {
        return array(
            "id" => $this->id,
            "user_id" => $this->user_id,
            "billing_address" => array(
                "street" => $this->billing_street,
                "city" => $this->billing_city,
                "state" => $this->billing_state,
                "postal_code" => $this->billing_postal_code,
                "country" => $this->billing_country,
                "phone" => $this->billing_phone
            ),
            "shipping_address" => array(
                "street" => $this->shipping_street,
                "city" => $this->shipping_city,
                "state" => $this->shipping_state,
                "postal_code" => $this->shipping_postal_code,
                "country" => $this->shipping_country,
                "phone" => $this->shipping_phone
            )
        );
    }
}
?>