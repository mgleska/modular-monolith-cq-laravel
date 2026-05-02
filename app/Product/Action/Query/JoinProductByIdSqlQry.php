<?php

declare(strict_types=1);

namespace Module\Product\Action\Query;

use Illuminate\Database\Query\JoinClause;
use Module\Product\Model\Product;
use Module\Shared\SqlDto\JoinSqlDto;

class JoinProductByIdSqlQry
{
    public function joinById(string $foreignSelector): JoinSqlDto
    {
        return new JoinSqlDto(
            Product::TABLE,
            function (JoinClause $join) use ($foreignSelector) {
                $join->on($foreignSelector, '=', Product::TABLE . '.id');
            },
            ['id' => 'int', 'ean' => 'string', 'name' => 'string', 'image_url' => 'string'],
            Product::TABLE,
            __CLASS__
        );
    }
}
