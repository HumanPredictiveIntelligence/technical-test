<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class OrderProcessor
{
    private $order;
    public $errors = [];
    private $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    // Process a new order with multiple items
    public function processOrder($orderData, $items)
    {
        try {
            $customer = Customer::where('email', $orderData['customer_email'])->first();
            if (!$customer) {
                $this->errors[] = 'Customer not found';
                return false;
            }

            $order = new Order();
            $order->customer_id = $customer->id;
            $order->status = 'pending';
            $order->total = 0;
            $order->save();

            $total = 0;

            foreach ($items as $item) {
                $product = Product::where('sku', $item['sku'])->first();

                if (!$product) {
                    continue;
                }

                $stock = DB::table('inventory')->where('product_id', $product->id)->value('quantity');

                if ($stock < $item['quantity']) {
                    $this->errors[] = "Insufficient stock for {$product->name}";
                    continue;
                }

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $product->id;
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $product->price;
                $orderItem->save();

                DB::table('inventory')
                    ->where('product_id', $product->id)
                    ->decrement('quantity', $item['quantity']);

                // Update customer purchase history - More inefficient queries
                $customer->total_purchases = OrderItem::where('order_id', $order->id)
                    ->sum(DB::raw('price * quantity'));
                $customer->save();

                $total += $item['quantity'] * $product->price;

                $notification = new Notification();
                $notification->type = 'item_processed';
                $notification->user_id = $customer->id;
                $notification->data = json_encode([
                    'product' => $product->name,
                    'quantity' => $item['quantity']
                ]);
                $notification->save();
            }

            $calculatedTotal = OrderItem::where('order_id', $order->id)
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->sum(DB::raw('order_items.quantity * products.price'));

            $order->total = $calculatedTotal;
            $order->save();

            if ($total > 1000) {
                Mail::raw('Thank you for your large order!', function($message) use ($customer) {
                    $message->to($customer->email);
                    $message->subject('Large Order Confirmation');
                });
            } else {
                Mail::raw('Thank you for your order!', function($message) use ($customer) {
                    $message->to($customer->email);
                    $message->subject('Order Confirmation');
                });
            }

            return true;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    public function doStuff($orderId)
    {
        $order = Order::find($orderId);
        $items = OrderItem::where('order_id', $orderId)->get();
        $total = 0;

        foreach ($items as $item) {
            $product = Product::find($item->product_id);
            $total += $product->price * $item->quantity;
        }

        return $total;
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = Order::find($orderId);

        if ($status == 'completed') {
            $order->status = 'completed';
            $order->completed_at = now();
            $order->save();

            $notification = new Notification();
            $notification->type = 'order_completed';
            $notification->user_id = $order->customer_id;
            $notification->data = json_encode([
                'order_id' => $orderId
            ]);
            $notification->save();
        }

        if ($status == 'cancelled') {
            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->save();

            $notification = new Notification();
            $notification->type = 'order_cancelled';
            $notification->user_id = $order->customer_id;
            $notification->data = json_encode([
                'order_id' => $orderId
            ]);
            $notification->save();
        }
    }
}
