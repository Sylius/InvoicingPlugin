<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Unit\Generator;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGenerator;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;

final class InvoiceFileNameGeneratorTest extends TestCase
{
    private InvoiceFileNameGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new InvoiceFileNameGenerator();
    }

    #[Test]
    public function it_implements_invoice_file_name_generator_interface(): void
    {
        self::assertInstanceOf(InvoiceFileNameGeneratorInterface::class, $this->generator);
    }

    #[Test]
    public function it_generates_invoice_file_name_based_on_its_number(): void
    {
        $result = $this->generator->generateForPdf('2020/01/02/000333');

        self::assertSame('2020_01_02_000333.pdf', $result);
    }

    #[Test]
    public function it_generates_scoped_file_name_when_scope_is_set(): void
    {
        $generator = new InvoiceFileNameGenerator('monthly');

        $result = $generator->generateForPdf('2020/01/02/000333');

        self::assertSame('monthly/2020_01_02_000333.pdf', $result);
    }

    #[Test]
    public function it_uses_global_scope_when_scope_is_invalid_or_null(): void
    {
        $generator = new InvoiceFileNameGenerator('invalid_scope');

        $result = $generator->generateForPdf('2020/01/02/000333');

        self::assertSame('2020_01_02_000333.pdf', $result);
    }
}
