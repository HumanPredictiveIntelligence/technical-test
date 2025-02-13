<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property int $price
 */
class OrderItem extends Model
{
    use HasFactory;
}
