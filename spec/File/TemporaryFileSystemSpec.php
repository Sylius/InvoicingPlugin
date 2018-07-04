<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\File;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\File\TemporaryFileSystem;
use Sylius\InvoicingPlugin\File\TemporaryFileSystemInterface;

final class TemporaryFileSystemSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TemporaryFileSystem::class);
    }

    public function it_implements_temporary_file_path_generator_interface(): void
    {
        $this->shouldImplement(TemporaryFileSystemInterface::class);
    }

    public function it_creates_a_file_with_given_content_and_path(): void
    {
        $filePath = sys_get_temp_dir() . '/' . vsprintf('invoice-%s.pdf', ['0000001']);
        $this->create('test', $filePath);
    }

    public function it_removes_file_with_given_path(): void
    {
        $filePath = sys_get_temp_dir() . '/' . vsprintf('invoice-%s.pdf', ['0000001']);
        $this->create('test', $filePath);

        $this->removeFile($filePath);
    }
}
