<?php
require_once 'models/Product.php';

class ProductController {
    
    public function getAllProducts($params = array()) {
        $product = new Product();
        
        $type = $params['type'] ?? null;
        $category = $params['category'] ?? null;
        $search = $params['search'] ?? null;
        
        $stmt = $product->read($type, $category, $search);
        $num = $stmt->rowCount();
        
        $products_arr = array();
        $products_arr["records"] = array();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            $product_item = array(
                "id" => $id,
                "name" => $name,
                "description" => $description,
                "price" => $price,
                "type" => $type,
                "category" => $category,
                "stock_quantity" => $stock_quantity,
                "image_url" => $image_url,
                "composer" => $composer,
                "instrument" => $instrument,
                "difficulty_level" => $difficulty_level,
                "duration_minutes" => $duration_minutes,
                "is_digital" => $is_digital
            );
            
            array_push($products_arr["records"], $product_item);
        }
        
        return json_encode($products_arr);
    }
    
    public function getProduct($id) {
        $product = new Product();
        $product->id = $id;
        $product->readOne();

        if($product->name) {
            $product_arr = array(
                "id" => $product->id,
                "name" => $product->name,
                "description" => $product->description,
                "price" => $product->price,
                "type" => $product->type,
                "category" => $product->category,
                "stock_quantity" => $product->stock_quantity,
                "digital_file_path" => $product->digital_file_path,
                "image_url" => $product->image_url,
                "composer" => $product->composer,
                "instrument" => $product->instrument,
                "difficulty_level" => $product->difficulty_level,
                "duration_minutes" => $product->duration_minutes,
                "is_digital" => $product->is_digital
            );

            return json_encode($product_arr);
        } else {
            http_response_code(404);
            return json_encode(array("message" => "Product not found."));
        }
    }

    public function addProduct() {
        $data = json_decode(file_get_contents("php://input"));

        if(
            !empty($data->name) &&
            !empty($data->price) &&
            !empty($data->type) &&
            !empty($data->category)
        ) {
            $product = new Product();
            $product->name = $data->name;
            $product->description = $data->description ?? '';
            $product->price = $data->price;
            $product->type = $data->type;
            $product->category = $data->category;
            $product->stock_quantity = $data->stock_quantity ?? 0;
            $product->digital_file_path = $data->digital_file_path ?? '';
            $product->image_url = $data->image_url ?? '';
            $product->composer = $data->composer ?? '';
            $product->instrument = $data->instrument ?? '';
            $product->difficulty_level = $data->difficulty_level ?? '';
            $product->duration_minutes = $data->duration_minutes ?? 0;
            $product->is_digital = $data->is_digital ?? 0;

            if($product->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Product was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create product."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
        }
    }
}
?>