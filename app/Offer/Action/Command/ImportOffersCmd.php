<?php

declare(strict_types=1);

namespace Module\Offer\Action\Command;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Module\Offer\Model\Offer;
use Module\Product\Action\Query\GetProductByEansQry;
use Module\Shared\Exception\AppValidationException;
use Module\Store\Action\Query\GetStoreByExternalIdQry;
use Ramsey\Uuid\Uuid;
use Throwable;

/** @codeCoverageIgnore */
class ImportOffersCmd
{
    public function __construct(
        private readonly GetStoreByExternalIdQry $getStoreByExternalId,
        private readonly GetProductByEansQry $getProductByEansQry,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(string $storeExternalId): void
    {
        $store = $this->getStoreByExternalId->handle($storeExternalId);

        if ($store === null) {
            throw AppValidationException::withMessages(['storeExternalId' => 'Can not find store w externalID=' . $storeExternalId]);
        }

        // Fake import from external API
        $apiData = $this->generateFakeData();

        $col = new Collection($apiData);

        $eans = $col->pluck('product_ean')->toArray();
        $mapped = $col->map(
            function ($item) use ($store) {
                $item['store_id'] = $store->id;

                return $item;
            }
        )->toArray();

        DB::beginTransaction();
        try {
            Offer::upsert($mapped, ['store_id', 'product_ean'], ['external_id', 'product_name', 'price', 'lowest_price']);
            Offer::query()
                ->where('store_id', $store->id)
                ->whereNotIn('product_ean', $eans)
                ->delete();

            $eanIdMap = Offer::query()
                ->where('store_id', $store->id)
                ->whereNull('product_id')
                ->get(['id', 'product_ean'])
                ->keyBy('product_ean')
                ->toArray();

            $products = $this->getProductByEansQry->handle(array_keys($eanIdMap));
            foreach ($products as $dto) {
                Offer::query()
                    ->where('store_id', $store->id)
                    ->where('id', $eanIdMap[$dto->ean])
                    ->update(['product_id' => $dto->id]);
            }

            DB::commit();
        }
        catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @return list<array<string, string|int|null>>
     */
    private function generateFakeData(): array
    {
        $template = [
            ['product_ean' => 'ean-1', 'product_name' => 'Red square imported', 'price' => 100, 'lowest_price' => null],
            ['product_ean' => 'ean-2', 'product_name' => 'Blue square imported', 'price' => 200, 'lowest_price' => null],
            ['product_ean' => 'ean-3', 'product_name' => 'Green square imported', 'price' => 300, 'lowest_price' => null],
            ['product_ean' => 'ean-4', 'product_name' => 'Red triangle imported', 'price' => 400, 'lowest_price' => null],
            ['product_ean' => 'ean-5', 'product_name' => 'Blue triangle imported', 'price' => 500, 'lowest_price' => null],
            ['product_ean' => 'ean-6', 'product_name' => 'Green triangle imported', 'price' => 600, 'lowest_price' => null],
            ['product_ean' => 'ean-7', 'product_name' => 'Red circle imported', 'price' => 700, 'lowest_price' => null],
            ['product_ean' => 'ean-8', 'product_name' => 'Blue circle imported', 'price' => 800, 'lowest_price' => null],
            ['product_ean' => 'ean-9', 'product_name' => 'Green circle imported', 'price' => 900, 'lowest_price' => null],
        ];

        $apiData = [];
        foreach ($template as $item) {
            if ($this->skipRandom(5)) {
                continue;
            }
            $item['external_id'] = Uuid::uuid4()->toString();
            if ($this->skipRandom(50)) {
                $item['product_name'] = null;
            }
            $price = $item['price'];
            $item['price'] = (int)round(0.9 * $price + (0.2 * $price) * (rand(0, 100) / 100));
            if ($this->skipRandom(80)) {
                $item['lowest_price'] = (int)round($item['price'] * 1.1);
            }
            $apiData[] = $item;
        }

        return $apiData;
    }

    private function skipRandom(int $percent): bool
    {
        return rand(1, 100) <= $percent;
    }
}
