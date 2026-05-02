<?php

declare(strict_types=1);

namespace Module\Store\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $external_id
 * @property string $name
 * @property string $address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store withoutTrashed()
 *
 * @noinspection PhpFullyQualifiedNameUsageInspection
 *
 * @mixin \Eloquent
 */
class Store extends Model
{
    use SoftDeletes;

    public const string TABLE = 'str_store';

    protected $table = self::TABLE;

    protected $fillable = [
        'name',
        'address',
        'external_id',
    ];
}
