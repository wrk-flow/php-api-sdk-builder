<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Contracts;

use DateTimeImmutable;
use DateTimeInterface;
use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

interface FileLogPathServiceContract
{
    public function getFilePath(string $baseDir, RequestInterface $request, string $id, ?string $type): string;

    /**
     * Returns the root folder name for the given date. This can be used to group the logs by day.
     */
    public function getRootDirectoryName(DateTimeInterface $date): string;

    /**
     * Checks if given directory is considered as root folder or not (matches the generated format).
     */
    public function isRootDirectory(string $directory): bool;


    /**
     * Build a list of folder names that should not be deleted (based on $config->keepLogFilesForDays).
     *
     * @return array<string, true>
     */
    public function getRootDirectoriesMapToNotDelete(LoggerConfigEntity $config, DateTimeImmutable $date): array;
}
