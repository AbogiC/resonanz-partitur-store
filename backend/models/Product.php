<?php
require_once 'config/database.php';

class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $price;
    public $type;
    public $category;
    public $stock_quantity;
    public $digital_file_path;
    public $image_url;
    public $composer;
    public $instrument;
    public $difficulty_level;
    public $duration_minutes;
    public $is_digital;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function read($type = null, $category = null, $search = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        if($type) {
            $query .= " AND type = :type";
        }
        
        if($category) {
            $query .= " AND category = :category";
        }
        
        if($search) {
            $query .= " AND (name LIKE :search OR description LIKE :search OR composer LIKE :search)";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if($type) {
            $stmt->bindParam(":type", $type);
        }
        
        if($category) {
            $stmt->bindParam(":category", $category);
        }
        
        if($search) {
            $search_term = "%$search%";
            $stmt->bindParam(":search", $search_term);
        }
        
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->type = $row['type'];
            $this->category = $row['category'];
            $this->stock_quantity = $row['stock_quantity'];
            $this->digital_file_path = $row['digital_file_path'];
            $this->image_url = $row['image_url'];
            $this->composer = $row['composer'];
            $this->instrument = $row['instrument'];
            $this->difficulty_level = $row['difficulty_level'];
            $this->duration_minutes = $row['duration_minutes'];
            $this->is_digital = $row['is_digital'];
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
                  name=:name, description=:description, price=:price, type=:type, category=:category,
                  stock_quantity=:stock_quantity, digital_file_path=:digital_file_path, image_url=:image_url,
                  composer=:composer, instrument=:instrument, difficulty_level=:difficulty_level,
                  duration_minutes=:duration_minutes, is_digital=:is_digital";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));
        $this->digital_file_path = htmlspecialchars(strip_tags($this->digital_file_path));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->composer = htmlspecialchars(strip_tags($this->composer));
        $this->instrument = htmlspecialchars(strip_tags($this->instrument));
        $this->difficulty_level = htmlspecialchars(strip_tags($this->difficulty_level));
        $this->duration_minutes = htmlspecialchars(strip_tags($this->duration_minutes));
        $this->is_digital = htmlspecialchars(strip_tags($this->is_digital));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":digital_file_path", $this->digital_file_path);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":composer", $this->composer);
        $stmt->bindParam(":instrument", $this->instrument);
        $stmt->bindParam(":difficulty_level", $this->difficulty_level);
        $stmt->bindParam(":duration_minutes", $this->duration_minutes);
        $stmt->bindParam(":is_digital", $this->is_digital);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function updateStock($quantity) {
        $query = "UPDATE " . $this->table_name . "
                  SET stock_quantity = stock_quantity - :quantity
                  WHERE id = :id AND stock_quantity >= :quantity";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
?>