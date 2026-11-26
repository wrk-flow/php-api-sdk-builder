<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Laravel\Commands;

use Illuminate\Console\Command;
use WrkFlow\ApiSdkBuilder\Http\Services\TemporaryResponseStorageService;

final class ClearResponseBuffersCommand extends Command
{
    protected $signature = 'api-sdk:response-buffers:clear';
    protected $description = 'Remove abandoned API SDK response buffers.';

    public function handle(TemporaryResponseStorageService $storage): int
    {
        $deleted = $storage->clearExpired(time() - 86400);
        $this->info(sprintf('Removed %d abandoned response buffer(s).', $deleted));

        return self::SUCCESS;
    }
}
