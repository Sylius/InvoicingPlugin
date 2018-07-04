<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\File;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\File\TemporaryFilePathGenerator;
use Sylius\InvoicingPlugin\File\TemporaryFilePathGeneratorInterface;

final class TemporaryFilePathGeneratorSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TemporaryFilePathGenerator::class);
    }

    public function it_implements_temporary_file_path_generator_interface(): void
    {
        $this->shouldImplement(TemporaryFilePathGeneratorInterface::class);
    }

    public function it_returns_temporary_file_path_for_given_pattern_and_parameters(): void
    {
        $this->generate('invoice-%s.pdf', '0000001')->shouldBeString();
    }
}
