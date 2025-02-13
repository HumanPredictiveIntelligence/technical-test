<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $sku
 * @property string $name
 * @property int $price
 */
class Product extends Model
{
    use HasFactory, HasTimestamps;
}
