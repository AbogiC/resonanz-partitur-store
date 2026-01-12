<?php
// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Start session
session_start();

// Include required files
require_once 'middleware/AuthMiddleware.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/ProductController.php';
require_once 'controllers/CartController.php';
require_once 'controllers/OrderController.php';
require_once 'controllers/PaymentController.php';

// Get request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Remove query string
$uri = parse_url($request_uri, PHP_URL_PATH);
$uri = str_replace('/api/', '', $uri);
$uri_parts = explode('/', $uri);

// Get request data
$data = json_decode(file_get_contents("php://input"), true);
if (empty($data)) {
    $data = $_REQUEST;
}

// Route the request
switch ($method) {
    case 'POST':
        switch ($uri_parts[0]) {
            case 'register':
                $auth = new AuthController();
                echo $auth->register($data);
                break;

            case 'login':
                $auth = new AuthController();
                echo $auth->login($data);
                break;

            case 'cart':
                AuthMiddleware::authenticate();
                $cart = new CartController();
                $user_id = $_SESSION['user_id'] ?? 1; // In production, get from token
                echo $cart->addToCart($user_id, $data);
                break;

            case 'order':
                AuthMiddleware::authenticate();
                $order = new OrderController();
                $user_id = $_SESSION['user_id'] ?? 1;
                echo $order->createOrder($user_id, $data);
                break;

            case 'payment-intent':
                AuthMiddleware::authenticate();
                $payment = new PaymentController();
                $user_id = $_SESSION['user_id'] ?? 1;
                echo $payment->createPaymentIntent($user_id, $data);
                break;

            case 'confirm-payment':
                AuthMiddleware::authenticate();
                $payment = new PaymentController();
                $user_id = $_SESSION['user_id'] ?? 1;
                echo $payment->confirmPayment($user_id, $data);
                break;

            case 'webhook':
                $payment = new PaymentController();
                echo $payment->handlePaymentWebhook($data);
                break;

            case 'products':
                $product = new ProductController();
                echo $product->addProduct();
                break;

            default:
                http_response_code(404);
                echo json_encode(array("message" => "Endpoint not found."));
                break;
        }
        break;

    case 'GET':
        switch ($uri_parts[0]) {
            case 'products':
                $product = new ProductController();
                if (isset($uri_parts[1])) {
                    echo $product->getProduct($uri_parts[1]);
                } else {
                    echo $product->getAllProducts($_GET);
                }
                break;

            case 'cart':
                AuthMiddleware::authenticate();
                $cart = new CartController();
                $user_id = $_SESSION['user_id'] ?? 1;
                echo $cart->getCart($user_id);
                break;

            case 'orders':
                AuthMiddleware::authenticate();
                $order = new OrderController();
                $user_id = $_SESSION['user_id'] ?? 1;
                if (isset($uri_parts[1])) {
                    echo $order->getOrderDetails($user_id, $uri_parts[1]);
                } else {
                    echo $order->getUserOrders($user_id);
                }
                break;

            case 'cart-summary':
                AuthMiddleware::authenticate();
                $cart = new CartController();
                $user_id = $_SESSION['user_id'] ?? 1;
                echo $cart->getCartSummary($user_id);
                break;

            case 'check-cart-stock':
                AuthMiddleware::authenticate();
                $cart = new CartController();
                $user_id = $_SESSION['user_id'] ?? 1;
                echo $cart->checkCartStock($user_id);
                break;

            default:
                http_response_code(404);
                echo json_encode(array("message" => "Endpoint not found."));
                break;
        }
        break;

    case 'PUT':
        switch ($uri_parts[0]) {
            case 'cart':
                AuthMiddleware::authenticate();
                if (isset($uri_parts[1])) {
                    $cart = new CartController();
                    $user_id = $_SESSION['user_id'] ?? 1;
                    echo $cart->updateCartItem($user_id, $uri_parts[1], $data);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(array("message" => "Endpoint not found."));
                break;
        }
        break;

    case 'DELETE':
        switch ($uri_parts[0]) {
            case 'cart':
                AuthMiddleware::authenticate();
                $cart = new CartController();
                $user_id = $_SESSION['user_id'] ?? 1;
                if (isset($uri_parts[1])) {
                    echo $cart->removeCartItem($user_id, $uri_parts[1]);
                } else {
                    echo $cart->clearCart($user_id);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(array("message" => "Endpoint not found."));
                break;
        }
        break;

    case 'cart':
        AuthMiddleware::authenticate();
        $cart = new CartController();
        $user_id = $_SESSION['user_id'] ?? 1;

        // Handle bulk update if array of items is provided
        if (isset($data['items']) && is_array($data['items'])) {
            $results = array();
            foreach ($data['items'] as $item) {
                if (isset($item['cart_id']) && isset($item['quantity'])) {
                    $result = json_decode($cart->updateCartItem($user_id, $item['cart_id'], $item), true);
                    $results[] = $result;
                }
            }
            echo json_encode(array(
                "message" => "Bulk update completed.",
                "results" => $results
            ));
        } elseif (isset($uri_parts[1])) {
            // Single item update
            echo $cart->updateCartItem($user_id, $uri_parts[1], $data);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>