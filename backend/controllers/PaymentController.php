<?php
require_once 'config/constants.php';
require_once 'models/Order.php';

class PaymentController {
    
    public function createPaymentIntent($user_id, $data) {
        // For production, use Stripe PHP library
        // This is a simplified version
        
        $order_id = $data['order_id'] ?? '';
        $amount = $data['amount'] ?? 0;
        
        if(empty($order_id) || $amount <= 0) {
            http_response_code(400);
            return json_encode(array("message" => "Invalid order data."));
        }
        
        // In production, create actual Stripe PaymentIntent
        // $stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);
        // $paymentIntent = $stripe->paymentIntents->create([
        //     'amount' => $amount * 100, // Convert to cents
        //     'currency' => 'usd',
        //     'metadata' => ['order_id' => $order_id]
        // ]);
        
        // For demo purposes
        $client_secret = 'pi_' . uniqid() . '_secret_' . uniqid();
        $payment_intent_id = 'pi_' . uniqid();
        
        return json_encode(array(
            "client_secret" => $client_secret,
            "payment_intent_id" => $payment_intent_id,
            "publishable_key" => STRIPE_PUBLISHABLE_KEY
        ));
    }
    
    public function handlePaymentWebhook($payload) {
        // Handle Stripe webhook events
        // This is where you would update order status based on payment success/failure
        
        $event_type = $payload['type'] ?? '';
        $payment_intent = $payload['data']['object'] ?? array();
        
        if($event_type === 'payment_intent.succeeded') {
            $order_id = $payment_intent['metadata']['order_id'] ?? '';
            $transaction_id = $payment_intent['id'];
            
            if($order_id) {
                // Update order status
                $order = new Order();
                $order->id = $order_id;
                $order->payment_status = 'completed';
                $order->status = 'processing';
                $order->transaction_id = $transaction_id;
                $order->updatePaymentStatus();
            }
        } elseif($event_type === 'payment_intent.payment_failed') {
            $order_id = $payment_intent['metadata']['order_id'] ?? '';
            
            if($order_id) {
                $order = new Order();
                $order->id = $order_id;
                $order->payment_status = 'failed';
                $order->updatePaymentStatus();
            }
        }
        
        return json_encode(array("message" => "Webhook received."));
    }
    
    public function confirmPayment($user_id, $data) {
        $order_id = $data['order_id'] ?? '';
        $payment_intent_id = $data['payment_intent_id'] ?? '';
        
        // In production, verify payment with Stripe
        // $stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);
        // $paymentIntent = $stripe->paymentIntents->retrieve($payment_intent_id);
        
        // For demo, simulate successful payment
        $order = new Order();
        $order->id = $order_id;
        $order->user_id = $user_id;
        $order_data = $order->readOne();
        
        if(!$order_data) {
            http_response_code(404);
            return json_encode(array("message" => "Order not found."));
        }
        
        if($order_data['payment_status'] === 'pending') {
            $order->payment_status = 'completed';
            $order->status = 'processing';
            $order->transaction_id = $payment_intent_id;
            
            if($order->updatePaymentStatus()) {
                return json_encode(array(
                    "message" => "Payment confirmed successfully.",
                    "order_number" => $order_data['order_number'],
                    "status" => "processing"
                ));
            }
        }
        
        return json_encode(array("message" => "Payment already processed."));
    }
}
?>