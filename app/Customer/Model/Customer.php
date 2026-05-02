<?php

declare(strict_types=1);

namespace Module\Customer\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Module\Customer\Action\Enum\CustomerStatusEnum;

/**
 * @property int $id
 * @property CustomerStatusEnum $status
 * @property int $selected_store
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 *
 * @noinspection PhpFullyQualifiedNameUsageInspection
 *
 * @mixin \Eloquent
 */
class Customer extends Model
{
    public const string TABLE = 'cst_customer';

    protected $table = self::TABLE;

    protected $fillable = [
        'name',
        'selected_store',
        'status',
    ];

    /**
     * @return array<string, class-string>
     */
    protected function casts(): array
    {
        return [
            'status' => CustomerStatusEnum::class,
        ];
    }
}
