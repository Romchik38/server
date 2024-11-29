<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Files;

use Romchik38\Server\Api\Services\FileLoaderInterface;

class FileLoader implements FileLoaderInterface
{
    protected string $data = '';

    /** 
     * @param string $prefix A path without trailing slash. Something like /some/path/to
     */
    public function __construct(protected string $prefix)
    {
        if (!is_dir($prefix)) {
            throw new FileLoaderException(sprintf(
                'Dir %s not exist',
                $prefix
            ));
        }

        if (str_ends_with($prefix, '/')) {
            $this->prefix = substr($prefix, 0, strlen($prefix) - 1);
        }
    }

    public function load(string $path): string
    {
        if (str_starts_with($path, '/')) {
            $fullPath = $this->prefix . $path;
        } else {
            $fullPath = sprintf('%s/%s', $this->prefix, $path);
        }

        if (!file_exists($fullPath)) {
            throw new FileLoaderException(sprintf(
                'File %s not exist',
                $fullPath
            ));
        }

        $fp = fopen($fullPath, 'r');

        if ($fp === false) {
            throw new FileLoaderException(
                sprintf(
                    'Can\'t open to read file %s',
                    $fullPath
                )
            );
        }

        $file = '';

        $chank = fread($fp, 1024);
        while ($chank !== false && $chank !== '') {
            if ($chank !== false) {
                $file .= $chank;
            }
            $chank = fread($fp, 1024);
        }

        $isClosed = fclose($fp);
        if ($isClosed === false) {
            throw new FileLoaderException(
                sprintf(
                    'Cannot close file %s',
                    $fullPath
                )
            );
        }

        return $file;
    }
}
