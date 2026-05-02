<?php

declare(strict_types=1);

namespace Module\Product\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $store_id
 * @property int $product_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductQuantity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductQuantity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductQuantity query()
 *
 * @noinspection PhpFullyQualifiedNameUsageInspection
 *
 * @mixin \Eloquent
 */
class ProductQuantity extends Model
{
    public const string TABLE = 'prd_product_quantity';

    protected $table = self::TABLE;

    protected $fillable = [
    ];
}
