<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $customer_id
 * @property mixed|string $status
 * @property int|mixed $total
 */
class Order extends Model
{
    use HasFactory;
}
