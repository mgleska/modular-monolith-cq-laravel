<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Module\Store\Model\Store;

class CheckStoreExistsQry
{
    public function check(int $id): bool
    {
        $store = Store::find($id);

        return $store !== null;
    }
}
