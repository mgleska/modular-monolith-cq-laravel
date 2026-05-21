<?php

declare(strict_types=1);

namespace Module\Product\Access\Console;

use Illuminate\Console\Command;
use Module\Product\Action\Command\ImportQuantityCmd;
use Throwable;

/** @codeCoverageIgnore */
class ImportProductsQuantityCon extends Command
{
    protected $signature = 'product:quantity';

    protected $description = 'Command to import quantities for each products in all stores (API)';

    /**
     * @throws Throwable
     */
    public function handle(ImportQuantityCmd $action): void
    {
        $this->info('Importing product quantity...');
        $action->handle();
        $this->info('Product quantity imported successfully!');
    }
}
