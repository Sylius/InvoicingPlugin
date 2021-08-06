<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Filesystem;

final class TemporaryFilesystem
{
    /** @var string */
    private $targetDirectory;

    public function __construct(?string $targetDirectory = null)
    {
        $this->targetDirectory = rtrim($targetDirectory ?? sys_get_temp_dir(), \DIRECTORY_SEPARATOR);
    }

    public function executeWithFile(string $filename, string $content, callable $callback): void
    {
        $filepath = $this->targetDirectory . \DIRECTORY_SEPARATOR . $filename;

        if (!file_put_contents($filepath, $content)) {
            throw new \RuntimeException(sprintf('Could not create file "%s"!', $filepath));
        }

        try {
            $callback($filepath);
        } finally {
            unlink($filepath);
        }
    }
}
