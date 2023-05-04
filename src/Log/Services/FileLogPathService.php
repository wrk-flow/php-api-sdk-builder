<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Services;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Log\Contracts\FileLogPathServiceContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

/**
 * Groups requests / responses:
 * - by day
 * - by host
 */
class FileLogPathService implements FileLogPathServiceContract
{
    public function getFilePath(string $baseDir, RequestInterface $request, string $id, ?string $type): string
    {
        $date = new DateTimeImmutable();
        $uriPath = $request->getUri()
            ->getPath();
        $cleanPath = str_replace(search: ['/', DIRECTORY_SEPARATOR], replace: '-', subject: $uriPath);

        return implode(
            separator: DIRECTORY_SEPARATOR,
            array: [
                $baseDir,
                $this->getRootDirectoryName($date),
                $request->getUri()
                    ->getHost(),
                $date->format(DATE_ATOM) . $cleanPath . ($type === null ? '' : '-' . $type) . '-' . $id,
            ]
        );
    }

    public function getRootDirectoryName(DateTimeInterface $date): string
    {
        // Ensure that the date is in local timezone
        return (new DateTime())
            ->setTimestamp($date->getTimestamp())
            ->format('Y-m-d');
    }

    public function isRootDirectory(string $directory): bool
    {
        return preg_match('#^\d{4}-\d{2}-\d{2}$#', $directory) === 1;
    }

    /**
     * Build a list of folder names generated from days that we want to leave. The rest will be deleted.
     *
     * @return array<string, true>
     */
    public function getRootDirectoriesMapToNotDelete(LoggerConfigEntity $config, DateTimeInterface $date): array
    {
        $allowedMap = [
            $this->getRootDirectoryName($date) => true,
        ];

        for ($day = 1; $day <= $config->keepLogFilesForDays; ++$day) {
            $newDate = (new DateTimeImmutable())->setTimestamp($date->getTimestamp());

            $leaveDate = $this->getRootDirectoryName($newDate->modify('-' . $day . ' days'));
            $allowedMap[$leaveDate] = true;
        }
        return $allowedMap;
    }
}
