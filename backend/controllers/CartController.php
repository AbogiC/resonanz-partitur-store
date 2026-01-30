<?php
require_once 'models/Cart.php';
require_once 'models/Product.php';

class CartController
{

    public function addToCart($user_id, $data)
    {
        $cart = new Cart();
        $cart->user_id = $user_id;
        $cart->product_id = $data['product_id'];
        $cart->quantity = $data['quantity'] ?? 1;

        // Check product stock
        $product = new Product();
        $product->id = $data['product_id'];
        $product->readOne();

        if (!$product->name) {
            http_response_code(404);
            return json_encode(array("message" => "Product not found."));
        }

        if ($product->stock_quantity < $cart->quantity && !$product->is_digital) {
            http_response_code(400);
            return json_encode(array(
                "message" => "Insufficient stock.",
                "available_stock" => $product->stock_quantity
            ));
        }

        if ($cart->addToCart()) {
            return json_encode(array(
                "message" => "Product added to cart.",
                "cart_item" => array(
                    "product_id" => $cart->product_id,
                    "quantity" => $cart->quantity
                )
            ));
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Unable to add product to cart."));
        }
    }

    public function getCart($user_id)
    {
        $cart = new Cart();
        $cart->user_id = $user_id;

        $stmt = $cart->getCartItems();
        $num = $stmt->rowCount();

        $cart_arr = array();
        $cart_arr["items"] = array();
        $cart_arr["total"] = $cart->getCartTotal();
        $cart_arr["item_count"] = $num;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $cart_item = array(
                "cart_id" => $id,
                "product_id" => $product_id,
                "name" => $name,
                "price" => $price,
                "type" => $type,
                "category" => $category,
                "image_url" => $image_url,
                "quantity" => $quantity,
                "item_total" => $item_total,
                "is_digital" => $is_digital,
                "stock_quantity" => $stock_quantity,
                "composer" => $composer,
                "instrument" => $instrument
            );

            array_push($cart_arr["items"], $cart_item);
        }

        return json_encode($cart_arr);
    }

    public function getCartItemCount($user_id)
    {
        $cart = new Cart();
        $cart->user_id = $user_id;

        $count = $cart->getCartItemCount();

        return json_encode(array("count" => $count));
    }

    public function increaseQuantityCartItem($cart_id)
    {
        try {
            $cart = new Cart();

            // Validate cart_id
            if (empty($cart_id) || !is_numeric($cart_id)) {
                http_response_code(400);
                return json_encode(array(
                    "message" => "Invalid cart ID.",
                    "success" => false
                ));
            }

            // Get current quantity
            $currentQuantity = $cart->getQuantityByCartId($cart_id);

            // Check if cart item exists
            if ($currentQuantity === false || $currentQuantity === null) {
                http_response_code(404);
                return json_encode(array(
                    "message" => "Cart item not found.",
                    "success" => false
                ));
            }

            // Set properties
            $cart->id = $cart_id;
            $cart->quantity = $currentQuantity + 1;

            // Check stock availability before increasing
            $productInfo = $cart->getProductInfoByCartId($cart_id);
            if ($productInfo && isset($productInfo['stock']) && !$productInfo['is_digital']) {
                if ($cart->quantity > $productInfo['stock']) {
                    http_response_code(400);
                    return json_encode(array(
                        "message" => "Insufficient stock. Available: " . $productInfo['stock'],
                        "success" => false,
                        "available_stock" => $productInfo['stock']
                    ));
                }
            }

            // Update quantity
            if ($cart->increaseQuantity($cart_id)) {
                return json_encode(array(
                    "message" => "Cart updated successfully.",
                    "success" => true,
                    "cart_item" => array(
                        "cart_id" => (int) $cart_id,
                        "quantity" => (int) $cart->quantity,
                        "product_id" => $productInfo['product_id'] ?? null,
                        "price" => $productInfo['price'] ?? null
                    )
                ), JSON_NUMERIC_CHECK);
            } else {
                http_response_code(500);
                return json_encode(array(
                    "message" => "Unable to update cart. Database error occurred.",
                    "success" => false
                ));
            }

        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(array(
                "message" => "Server error: " . $e->getMessage(),
                "success" => false
            ));
        }
    }

    public function decreaseQuantityCartItem($cart_id)
    {
        try {
            $cart = new Cart();

            // Validate cart_id
            if (empty($cart_id) || !is_numeric($cart_id)) {
                http_response_code(400);
                return json_encode(array(
                    "message" => "Invalid cart ID.",
                    "success" => false
                ));
            }

            // Get current quantity
            $currentQuantity = $cart->getQuantityByCartId($cart_id);

            // Check if cart item exists
            if ($currentQuantity === false || $currentQuantity === null) {
                http_response_code(404);
                return json_encode(array(
                    "message" => "Cart item not found.",
                    "success" => false
                ));
            }

            // Set properties
            $cart->id = $cart_id;
            $cart->user_id = $_SESSION['user_id'] ?? 1;
            $cart->quantity = $currentQuantity - 1;

            // Delete item if quantity goes below 1
            if ($cart->quantity < 1) {
                if ($cart->removeItem()) {
                    return json_encode(array(
                        "message" => "Item removed from cart.",
                        "success" => true,
                        "removed" => true,
                        "cart_id" => (int) $cart_id
                    ));
                }
            } else {
                // Update quantity
                if ($cart->decreaseQuantity($cart_id)) {
                    return json_encode(array(
                        "message" => "Cart updated successfully.",
                        "success" => true,
                        "cart_item" => array(
                            "cart_id" => (int) $cart_id,
                            "quantity" => (int) $cart->quantity,
                            "product_id" => $productInfo['product_id'] ?? null,
                            "price" => $productInfo['price'] ?? null
                        )
                    ), JSON_NUMERIC_CHECK);
                } else {
                    http_response_code(500);
                    return json_encode(array(
                        "message" => "Unable to update cart. Database error occurred.",
                        "success" => false
                    ));
                }
            }

        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(array(
                "message" => "Server error: " . $e->getMessage(),
                "success" => false
            ));
        }
    }

    public function updateCartItem($user_id, $cart_id, $data)
    {
        $cart = new Cart();
        $cart->id = $cart_id;
        $cart->user_id = $user_id;
        $cart->quantity = $data['quantity'];

        // First get the current cart item to check product stock
        $query = "SELECT c.product_id, p.stock_quantity, p.is_digital 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.id = :cart_id AND c.user_id = :user_id";

        $stmt = $cart->conn->prepare($query);
        $stmt->bindParam(":cart_id", $cart_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check stock for physical products
            if (!$row['is_digital'] && $row['stock_quantity'] < $cart->quantity) {
                http_response_code(400);
                return json_encode(array(
                    "message" => "Insufficient stock. Maximum available: " . $row['stock_quantity'],
                    "available_stock" => $row['stock_quantity']
                ));
            }
        }

        if ($cart->updateQuantity()) {
            return json_encode(array(
                "message" => "Cart updated.",
                "cart_item" => array(
                    "cart_id" => $cart_id,
                    "quantity" => $cart->quantity
                )
            ));
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Unable to update cart."));
        }
    }

    public function removeCartItem($user_id, $cart_id)
    {
        $cart = new Cart();
        $cart->id = $cart_id;
        $cart->user_id = $user_id;

        if ($cart->removeItem()) {
            return json_encode(array(
                "message" => "Item removed from cart.",
                "removed_cart_id" => $cart_id
            ));
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Unable to remove item."));
        }
    }

    public function clearCart($user_id)
    {
        $cart = new Cart();
        $cart->user_id = $user_id;

        if ($cart->clearCart()) {
            return json_encode(array("message" => "Cart cleared."));
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Unable to clear cart."));
        }
    }

    public function getCartSummary($user_id)
    {
        $cart = new Cart();
        $cart->user_id = $user_id;

        $query = "SELECT COUNT(*) as item_count, 
                         SUM(c.quantity * p.price) as total_amount,
                         SUM(CASE WHEN p.is_digital = 1 THEN 1 ELSE 0 END) as digital_items,
                         SUM(CASE WHEN p.is_digital = 0 THEN 1 ELSE 0 END) as physical_items
                  FROM cart c
                  JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = :user_id";

        $stmt = $cart->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $summary = array(
            "item_count" => $row['item_count'] ?? 0,
            "total_amount" => $row['total_amount'] ?? 0,
            "digital_items" => $row['digital_items'] ?? 0,
            "physical_items" => $row['physical_items'] ?? 0
        );

        return json_encode($summary);
    }

    public function checkCartStock($user_id)
    {
        $cart = new Cart();
        $cart->user_id = $user_id;

        $query = "SELECT c.id as cart_id, p.id as product_id, p.name, 
                         p.stock_quantity, c.quantity as cart_quantity,
                         p.is_digital
                  FROM cart c
                  JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = :user_id AND p.is_digital = 0";

        $stmt = $cart->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $out_of_stock_items = array();
        $low_stock_items = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['stock_quantity'] < $row['cart_quantity']) {
                $out_of_stock_items[] = array(
                    "cart_id" => $row['cart_id'],
                    "product_id" => $row['product_id'],
                    "name" => $row['name'],
                    "required" => $row['cart_quantity'],
                    "available" => $row['stock_quantity']
                );
            } elseif ($row['stock_quantity'] <= 5) {
                $low_stock_items[] = array(
                    "product_id" => $row['product_id'],
                    "name" => $row['name'],
                    "stock" => $row['stock_quantity']
                );
            }
        }

        $stock_check = array(
            "out_of_stock" => $out_of_stock_items,
            "low_stock" => $low_stock_items,
            "has_issues" => count($out_of_stock_items) > 0
        );

        return json_encode($stock_check);
    }
}
?>