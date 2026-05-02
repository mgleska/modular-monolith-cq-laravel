<?php

declare(strict_types=1);

namespace Module\Offer\Action\Command;

use Illuminate\Support\Facades\DB;
use Module\Offer\Action\Dto\Admin\ChangeVisibilityDto;
use Module\Offer\Model\Offer;
use Module\Shared\Exception\AppEntityVersionException;
use Throwable;

class ChangeVisibilityCmd
{
    /**
     * @throws Throwable
     */
    public function handle(ChangeVisibilityDto $dto): void
    {
        DB::beginTransaction();

        try {
            $offer = Offer::lockForUpdate()->findOrFail($dto->id);

            if ($offer->version !== $dto->version) {
                throw new AppEntityVersionException;
            }

            $offer->visible = $dto->visible;
            $offer->version = $offer->version + 1;
            $offer->save();

            DB::commit();
        }
        catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
