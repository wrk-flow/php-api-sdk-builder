<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Laravel\Commands;

use Illuminate\Console\Command;
use WrkFlow\ApiSdkBuilder\Log\Actions\ClearFileLogsAction;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

class ClearFileLogsCommand extends Command
{
    protected $signature = 'api-sdk:logs:clear';
    protected $description = 'Clears request and response logs based on keep logs days config.';

    public function handle(ClearFileLogsAction $clearFileLogsAction, LoggerConfigEntity $config): void
    {
        if ($clearFileLogsAction->execute($config)) {
            $this->info('Logs cleared');
        } else {
            $this->info('No logs to clear');
        }
    }
}
