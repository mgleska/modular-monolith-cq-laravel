<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Illuminate\Database\Query\JoinClause;
use Module\Shared\SqlDto\JoinSqlDto;
use Module\Store\Model\Store;

class JoinStoreByIdSqlQry
{
    public function joinById(string $foreignSelector, ?string $alias = null): JoinSqlDto
    {
        return new JoinSqlDto(
            Store::TABLE . ($alias ? " AS $alias" : ''),
            function (JoinClause $join) use ($foreignSelector, $alias) {
                $join->on($foreignSelector, '=', ($alias ?? Store::TABLE) . '.id');
            },
            ['id' => 'int', 'name' => 'string'],
            $alias ?? Store::TABLE,
            __CLASS__
        );
    }
}
