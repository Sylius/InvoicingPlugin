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

namespace Tests\Sylius\InvoicingPlugin\Unit\Fixture;

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\InvoicingPlugin\Fixture\ShopBillingDataFixture;

final class ShopBillingDataFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    #[Test]
    public function shop_billing_data_are_optional(): void
    {
        self::assertConfigurationIsValid([[]], 'custom');
    }

    #[Test]
    public function shop_billing_data_can_be_generated_randomly(): void
    {
        self::assertConfigurationIsValid([['random' => 4]], 'random');
        self::assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    #[Test]
    public function shop_billing_data_channel_code_is_optional(): void
    {
        self::assertConfigurationIsValid([['custom' => [['channel_code' => 'CUSTOM']]]], 'custom.*.channel_code');
    }

    #[Test]
    public function shop_billing_data_company_is_optional(): void
    {
        self::assertConfigurationIsValid([['custom' => [['company' => 'Custom']]]], 'custom.*.company');
    }

    #[Test]
    public function shop_billing_data_country_code_is_optional(): void
    {
        self::assertConfigurationIsValid([['custom' => [['country_code' => 'pl_PL']]]], 'custom.*.country_code');
    }

    #[Test]
    public function shop_billing_data_city_is_optional(): void
    {
        self::assertConfigurationIsValid([['custom' => [['city' => 'Custom']]]], 'custom.*.city');
    }

    #[Test]
    public function shop_billing_data_postcode_is_optional(): void
    {
        self::assertConfigurationIsValid([['custom' => [['postcode' => '12345']]]], 'custom.*.postcode');
    }

    #[Test]
    public function shop_billing_data_tax_id_is_optional(): void
    {
        self::assertConfigurationIsValid([['custom' => [['tax_id' => '12345']]]], 'custom.*.tax_id');
    }

    #[Test]
    public function shop_billing_data_street_address_is_optional(): void
    {
        self::assertConfigurationIsValid([['custom' => [['street_address' => 'Custom street address']]]], 'custom.*.street_address');
    }

    /**
     * @inheritdoc
     */
    protected function getConfiguration(): ShopBillingDataFixture
    {
        return new ShopBillingDataFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock(),
        );
    }
}
