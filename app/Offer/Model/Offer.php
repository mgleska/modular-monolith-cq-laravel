<?php

declare(strict_types=1);

namespace Module\Offer\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $version
 * @property int $store_id
 * @property string $external_id
 * @property string $product_ean
 * @property string|null $product_name
 * @property int $price
 * @property int|null $lowest_price
 * @property bool $visible
 * @property int|null $product_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer query()
 *
 * @noinspection PhpFullyQualifiedNameUsageInspection
 *
 * @mixin \Eloquent
 */
class Offer extends Model
{
    public const string TABLE = 'ofr_offer';

    protected $table = self::TABLE;

    protected $fillable = [
        'version',
        'store_id',
        'external_id',
        'product_ean',
        'product_name',
        'price',
        'lowest_price',
        'visible',
        'product_id',
    ];

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
        ];
    }
}
