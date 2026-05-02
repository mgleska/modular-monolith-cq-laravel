<?php

declare(strict_types=1);

namespace Module\Offer\Access\Console;

use Illuminate\Console\Command;
use Module\Offer\Action\Command\ImportOffersCmd;
use Throwable;

class ImportOffersCon extends Command
{
    protected $signature = 'offer:import {storeRid : store exteral ID}';

    protected $description = 'Command to import offers for given store.';

    /**
     * @throws Throwable
     */
    public function handle(ImportOffersCmd $action): void
    {
        $storeRid = $this->argument('storeRid');

        $this->info("Importing offers for store with rid: $storeRid ...");
        $action->handle($storeRid);
        $this->info('Offers imported successfully!');
    }
}
