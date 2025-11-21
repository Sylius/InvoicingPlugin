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

namespace Tests\Sylius\InvoicingPlugin\Unit\Fixture\Listener;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;
use Sylius\InvoicingPlugin\Fixture\Listener\InvoicesPurgerListener;
use Symfony\Component\Filesystem\Filesystem;

final class InvoicesPurgerListenerTest extends TestCase
{
    #[Test]
    public function it_removes_invoices_before_fixture_suite(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $suite = $this->createMock(SuiteInterface::class);

        $filesystem
            ->expects(self::once())
            ->method('remove')
            ->with('path/to/invoices/');

        $listener = new InvoicesPurgerListener($filesystem, 'path/to/invoices/');
        $listener->beforeSuite(new SuiteEvent($suite), []);
    }
}
