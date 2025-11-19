<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Unit\Generator;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Generator\InvoiceIdentifierGenerator;
use Sylius\InvoicingPlugin\Generator\UuidInvoiceIdentifierGenerator;

final class UuidInvoiceIdentifierGeneratorTest extends TestCase
{
    private UuidInvoiceIdentifierGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new UuidInvoiceIdentifierGenerator();
    }

    #[Test]
    public function it_is_an_invoice_identifier_generator(): void
    {
        self::assertInstanceOf(InvoiceIdentifierGenerator::class, $this->generator);
    }

    #[Test]
    public function it_returns_a_string(): void
    {
        $result = $this->generator->generate();

        self::assertIsString($result);
    }

    #[Test]
    public function it_returns_two_different_strings_on_subsequent_calls(): void
    {
        $firstResult = $this->generator->generate();
        $secondResult = $this->generator->generate();

        self::assertNotEquals($firstResult, $secondResult);
    }
}
