<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $type
 * @property $user_id
 * @property false|string $data
 */
class Notification extends Model
{
    use HasFactory, HasTimestamps;
}
