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

namespace spec\Sylius\InvoicingPlugin\Fixture\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;
use Symfony\Component\Filesystem\Filesystem;

final class InvoicesPurgerListenerSpec extends ObjectBehavior
{
    public function let(Filesystem $filesystem): void
    {
        $this->beConstructedWith($filesystem, 'path/to/invoices/');
    }

    public function it_removes_invoices_before_fixture_suite(Filesystem $filesystem, SuiteInterface $suite): void
    {
        $filesystem->remove('path/to/invoices/')->shouldBeCalled();

        $this->beforeSuite(new SuiteEvent($suite->getWrappedObject()), []);
    }
}
