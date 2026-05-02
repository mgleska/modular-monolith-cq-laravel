<?php

declare(strict_types=1);

namespace Module\Product\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $ean
 * @property string $name
 * @property string|null $image_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 *
 * @noinspection PhpFullyQualifiedNameUsageInspection
 *
 * @mixin \Eloquent
 */
class Product extends Model
{
    public const string TABLE = 'prd_product';

    protected $table = self::TABLE;

    protected $fillable = [
        'ean',
        'name',
        'image_url',
    ];
}
