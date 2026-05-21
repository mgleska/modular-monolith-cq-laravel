<?php

declare(strict_types=1);

namespace Module\Product\Action\Command;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Module\Product\Model\Product;
use Throwable;

/** @codeCoverageIgnore */
class ImportProductsCmd
{
    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        // Fake import from external API
        $apiData = [
            ['ean' => 'ean-1', 'name' => 'Red square', 'image' => 'red-square.png'],
            ['ean' => 'ean-2', 'name' => 'Blue square', 'image' => 'blue-square.png'],
            ['ean' => 'ean-3', 'name' => 'Green square', 'image' => 'green-square.png'],
            ['ean' => 'ean-4', 'name' => 'Red triangle', 'image' => 'red-triangle.png'],
            ['ean' => 'ean-5', 'name' => 'Blue triangle', 'image' => 'blue-triangle.png'],
            ['ean' => 'ean-6', 'name' => 'Green triangle', 'image' => 'green-triangle.png'],
            ['ean' => 'ean-7', 'name' => 'Red circle', 'image' => 'red-circle.png'],
            ['ean' => 'ean-8', 'name' => 'Blue circle', 'image' => 'blue-circle.png'],
            ['ean' => 'ean-9', 'name' => 'Green circle', 'image' => 'green-circle.png'],
        ];

        $col = new Collection($apiData);

        $eans = $col->pluck('ean')->toArray();
        $mapped = $col->map(
            function ($item) {
                $item['image_url'] = $item['image'];
                unset($item['image']);

                return $item;
            }
        )->toArray();

        DB::beginTransaction();
        try {
            Product::upsert($mapped, ['ean'], ['name', 'image_url']);
            Product::query()
                ->whereNotIn('ean', $eans)
                ->delete();
            DB::commit();
        }
        catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
