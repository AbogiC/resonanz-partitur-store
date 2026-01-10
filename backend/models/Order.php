<?php
require_once 'config/database.php';

class Order {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $user_id;
    public $order_number;
    public $total_amount;
    public $status;
    public $payment_method;
    public $payment_status;
    public $shipping_address;
    public $billing_address;
    public $transaction_id;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $this->order_number = 'ORD' . strtoupper(uniqid());
        
        $query = "INSERT INTO " . $this->table_name . "
                SET user_id = :user_id,
                    order_number = :order_number,
                    total_amount = :total_amount,
                    shipping_address = :shipping_address,
                    billing_address = :billing_address,
                    payment_method = :payment_method";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":order_number", $this->order_number);
        $stmt->bindParam(":total_amount", $this->total_amount);
        $stmt->bindParam(":shipping_address", $this->shipping_address);
        $stmt->bindParam(":billing_address", $this->billing_address);
        $stmt->bindParam(":payment_method", $this->payment_method);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function readByUser() {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE user_id = :user_id 
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePaymentStatus() {
        $query = "UPDATE " . $this->table_name . "
                SET payment_status = :payment_status,
                    status = :status,
                    transaction_id = :transaction_id
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":payment_status", $this->payment_status);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":transaction_id", $this->transaction_id);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
}
?>