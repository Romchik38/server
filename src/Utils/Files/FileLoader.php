<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Files;

use function fclose;
use function file_exists;
use function fopen;
use function fread;
use function is_dir;
use function sprintf;

class FileLoader implements FileLoaderInterface
{
    public function load(string $path): string
    {
        if (! file_exists($path)) {
            throw new FileLoaderException(sprintf('File %s not exist', $path));
        }

        if (is_dir($path)) {
            throw new FileLoaderException(sprintf('File %s is a directory', $path));
        }

        $fp = fopen($path, 'r');

        if ($fp === false) {
            throw new FileLoaderException(sprintf('Can\'t open to read file %s', $path));
        }

        $file = '';

        $chank = fread($fp, 1024);
        if ($chank === false) {
            fclose($fp);
            throw new FileLoaderException(sprintf('Cannot read file %s', $path));
        }
        while ($chank !== '') {
            $file .= $chank;
            $chank = fread($fp, 1024);
            if ($chank === false) {
                fclose($fp);
                throw new FileLoaderException(sprintf('Cannot close file %s', $path));
            }
        }

        $isClosed = fclose($fp);
        if ($isClosed === false) {
            throw new FileLoaderException(sprintf('Cannot close file %s', $path));
        }

        return $file;
    }
}
