<?php

declare(strict_types=1);

namespace Module\Product\Access\Console;

use Illuminate\Console\Command;
use Module\Product\Action\Command\ImportProductsCmd;
use Throwable;

/** @codeCoverageIgnore */
class ImportProductsCon extends Command
{
    protected $signature = 'product:import';

    protected $description = 'Command to import products from external dictionary (API)';

    /**
     * @throws Throwable
     */
    public function handle(ImportProductsCmd $action): void
    {
        $this->info('Importing products from external dictionary...');
        $action->handle();
        $this->info('Products imported successfully!');
    }
}
