<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Actions;

use DateTimeImmutable;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use WrkFlow\ApiSdkBuilder\Log\Contracts\FileLogPathServiceContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

class ClearFileLogsAction
{
    public function __construct(
        private readonly FilesystemOperator $filesystemOperator,
        private readonly FileLogPathServiceContract $fileLogPathService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(LoggerConfigEntity $config): bool
    {
        $leaveDirectoriesMap = $this->fileLogPathService
            ->getRootDirectoriesMapToNotDelete(config: $config, date: new DateTimeImmutable());

        $this->logger->debug('Clearing file logs', [
            'days' => $config->keepLogFilesForDays,
            'will_leave' => array_keys($leaveDirectoriesMap),
        ]);

        $folders = $this->filesystemOperator->listContents($config->fileBaseDir);
        $deleted = 0;
        foreach ($folders as $folder) {
            if ($folder->isDir() === false) {
                continue;
            }

            $path = $folder->path();
            $pathComponents = explode(DIRECTORY_SEPARATOR, $path);
            $directory = end($pathComponents);

            // Is the folder in correct format (Y-m-d date).
            if ($this->fileLogPathService->isRootDirectory($directory) === false) {
                continue;
            }

            if (array_key_exists($directory, $leaveDirectoriesMap)) {
                continue;
            }

            $this->logger->debug('Deleting log requests folder at ' . $path);
            $this->filesystemOperator->deleteDirectory($path);
            ++$deleted;
        }

        return $deleted > 0;
    }
}
