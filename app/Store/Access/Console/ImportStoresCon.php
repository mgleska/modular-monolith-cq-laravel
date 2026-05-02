<?php

declare(strict_types=1);

namespace Module\Store\Access\Console;

use Illuminate\Console\Command;
use Module\Store\Action\Command\ImportStoresCmd;
use Throwable;

class ImportStoresCon extends Command
{
    protected $signature = 'store:import';

    protected $description = 'Command to import stores from external dictionary (API)';

    /**
     * @throws Throwable
     */
    public function handle(ImportStoresCmd $action): void
    {
        $this->info('Importing stores from external dictionary...');
        $action->handle();
        $this->info('Stores imported successfully!');
    }
}
