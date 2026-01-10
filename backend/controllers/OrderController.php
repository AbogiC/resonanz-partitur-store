
**controllers/OrderController.php**
```php
<?php
require_once 'models/Order.php';
require_once 'models/OrderItem.php';
require_once 'models/Cart.php';
require_once 'models/Product.php';

class OrderController {
    
    public function createOrder($user_id, $data) {
        // Get cart items
        $cart = new Cart();
        $cart->user_id = $user_id;
        
        $stmt = $cart->getCartItems();
        
        if($stmt->rowCount() == 0) {
            http_response_code(400);
            return json_encode(array("message" => "Cart is empty."));
        }
        
        // Calculate total and check stock
        $total_amount = 0;
        $order_items = array();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(!$row['is_digital'] && $row['stock_quantity'] < $row['quantity']) {
                http_response_code(400);
                return json_encode(array(
                    "message" => "Insufficient stock for product: " . $row['name'],
                    "product_id" => $row['product_id']
                ));
            }
            
            $item_total = $row['quantity'] * $row['price'];
            $total_amount += $item_total;
            
            $order_items[] = array(
                'product_id' => $row['product_id'],
                'quantity' => $row['quantity'],
                'unit_price' => $row['price'],
                'is_digital' => $row['is_digital']
            );
        }
        
        // Create order
        $order = new Order();
        $order->user_id = $user_id;
        $order->total_amount = $total_amount;
        $order->shipping_address = $data['shipping_address'] ?? '';
        $order->billing_address = $data['billing_address'] ?? $order->shipping_address;
        $order->payment_method = $data['payment_method'] ?? 'stripe';
        
        if(!$order->create()) {
            http_response_code(500);
            return json_encode(array("message" => "Unable to create order."));
        }
        
        // Create order items and update stock
        foreach($order_items as $item) {
            $order_item = new OrderItem();
            $order_item->order_id = $order->id;
            $order_item->product_id = $item['product_id'];
            $order_item->quantity = $item['quantity'];
            $order_item->unit_price = $item['unit_price'];
            
            if($item['is_digital']) {
                // Generate digital download URL
                $order_item->digital_download_url = 'downloads/' . uniqid() . '.pdf';
            }
            
            $order_item->create();
            
            // Update product stock for physical items
            if(!$item['is_digital']) {
                $product = new Product();
                $product->id = $item['product_id'];
                $product->updateStock($item['quantity']);
            }
        }
        
        // Clear cart
        $cart->clearCart();
        
        return json_encode(array(
            "message" => "Order created successfully.",
            "order_id" => $order->id,
            "order_number" => $order->order_number,
            "total_amount" => $total_amount
        ));
    }
    
    public function getUserOrders($user_id) {
        $order = new Order();
        $order->user_id = $user_id;
        
        $stmt = $order->readByUser();
        $num = $stmt->rowCount();
        
        $orders_arr = array();
        $orders_arr["records"] = array();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            $order_item = array(
                "id" => $id,
                "order_number" => $order_number,
                "total_amount" => $total_amount,
                "status" => $status,
                "payment_status" => $payment_status,
                "payment_method" => $payment_method,
                "created_at" => $created_at
            );
            
            array_push($orders_arr["records"], $order_item);
        }
        
        return json_encode($orders_arr);
    }
    
    public function getOrderDetails($user_id, $order_id) {
        $order = new Order();
        $order->id = $order_id;
        $order->user_id = $user_id;
        
        $order_data = $order->readOne();
        
        if(!$order_data) {
            http_response_code(404);
            return json_encode(array("message" => "Order not found."));
        }
        
        // Get order items
        $order_item = new OrderItem();
        $items_stmt = $order_item->readByOrder($order_id);
        
        $items_arr = array();
        while($row = $items_stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            $item = array(
                "product_id" => $product_id,
                "name" => $name,
                "type" => $type,
                "quantity" => $quantity,
                "unit_price" => $unit_price,
                "total" => $quantity * $unit_price,
                "is_digital" => $is_digital,
                "digital_download_url" => $digital_download_url
            );
            
            array_push($items_arr, $item);
        }
        
        $order_data['items'] = $items_arr;
        
        return json_encode($order_data);
    }
}
?>