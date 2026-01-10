<?php
require_once 'config/database.php';

class OrderItem {
    private $conn;
    private $table_name = "order_items";

    public $id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $unit_price;
    public $digital_download_url;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET order_id = :order_id,
                    product_id = :product_id,
                    quantity = :quantity,
                    unit_price = :unit_price,
                    digital_download_url = :digital_download_url";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":unit_price", $this->unit_price);
        $stmt->bindParam(":digital_download_url", $this->digital_download_url);
        
        return $stmt->execute();
    }

    public function readByOrder($order_id) {
        $query = "SELECT oi.*, p.name, p.type, p.image_url, p.is_digital
                  FROM " . $this->table_name . " oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $order_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>