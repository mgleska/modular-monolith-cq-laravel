<?php

declare(strict_types=1);

namespace Module\Product\Action\Query;

use Module\Product\Action\Dto\ProductShortDto;
use Module\Product\Model\Product;

class GetProductByEansQry
{
    /**
     * @param  string[]  $eans
     * @return ProductShortDto[]
     */
    public function handle(array $eans): array
    {
        if (empty($eans)) {
            return [];
        }

        $col = Product::query()
            ->whereIn('ean', $eans)
            ->get(['id', 'ean']);

        return ProductShortDto::collect($col, 'array');
    }
}
