<?php

declare(strict_types=1);

namespace Module\Store\Action\Command;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Module\Store\Model\Store;
use Throwable;

/** @codeCoverageIgnore */
class ImportStoresCmd
{
    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        // Fake import from external API
        $apiData = [
            ['rid' => 'r001', 'name' => 'Poznań', 'address' => 'ul. Bałtycka 1'],
            ['rid' => 'r002', 'name' => 'Luboń', 'address' => 'ul. Poznańska 1'],
            ['rid' => 'r003', 'name' => 'Czerwonak', 'address' => 'ul. Poznańska 1'],
            ['rid' => 'r004', 'name' => 'Kórnik', 'address' => 'ul. Poznańska 1'],
            ['rid' => 'r005', 'name' => 'Mosina', 'address' => 'ul. Poznańska 1'],
        ];

        $col = new Collection($apiData);

        $ids = $col->pluck('rid')->toArray();
        $mapped = $col->map(
            function ($item) {
                $item['external_id'] = $item['rid'];
                $item['deleted_at'] = null;
                unset($item['rid']);

                return $item;
            }
        )->toArray();

        DB::beginTransaction();
        try {
            Store::upsert($mapped, ['external_id'], ['name', 'address', 'deleted_at']);
            Store::query()
                ->whereNotIn('external_id', $ids)
                ->delete();
            DB::commit();
        }
        catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
