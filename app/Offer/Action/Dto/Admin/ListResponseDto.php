<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use Abrha\LaravelDataDocs\Attributes\Example;
use Illuminate\Contracts\Pagination\Paginator;
use Spatie\LaravelData\Data;
use stdClass;

class ListResponseDto extends Data
{
    public function __construct(
        /** @var OfferShortDto[] */
        public readonly array $items,
        #[Example(1)]
        public readonly int $page,
        #[Example(10)]
        public readonly int $perPage,
    ) {}

    /**
     * @param  Paginator<array-key, stdClass>  $paginator
     */
    public static function fromPaginator(Paginator $paginator): self
    {
        return new self(
            items: OfferShortDto::collect($paginator->items(), 'array'),
            page: $paginator->currentPage(),
            perPage: $paginator->perPage(),
        );
    }
}
