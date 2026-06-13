<?php

namespace App\Console\Commands;

use App\Services\Inventory\InventoryMenuSyncService;
use Illuminate\Console\Command;

class SyncInventoryMenusCommand extends Command
{
    protected $signature = 'inventory:sync-menus {--force : Ignore sync cache TTL}';

    protected $description = 'Sync POS menus from the inventory microservice';

    public function handle(InventoryMenuSyncService $sync): int
    {
        if ($this->option('force')) {
            cache()->forget('inventory.menu_sync.last_run');
        }

        if ($sync->sync()) {
            $this->info('Menus synced from inventory service.');

            return self::SUCCESS;
        }

        $this->warn('Inventory menu sync did not run or failed. Check logs and .env configuration.');

        return self::FAILURE;
    }
}
