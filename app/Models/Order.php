<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $reference
 * @property array<string, float|int|string>[] $items
 * @property string $status
 * @property int $total
 * @property int $discount
 * @property int $currency
 */
class Order extends Model
{
    public const STATUS_PENDING = 'pending';

    protected $casts = [
        'items' => 'array',
    ];
}
