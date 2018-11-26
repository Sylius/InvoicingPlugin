<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Unit\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Fixture\ShopBillingDataFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

final class ShopBillingDataFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function shop_billing_data_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /** @test */
    public function shop_billing_data_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /** @test */
    public function shop_billing_data_channel_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['channel_code' => 'CUSTOM']]]], 'custom.*.channel_code');
    }

    /** @test */
    public function shop_billing_data_company_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['company' => 'Custom']]]], 'custom.*.company');
    }

    /** @test */
    public function shop_billing_data_country_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['country_code' => 'pl_PL']]]], 'custom.*.country_code');
    }

    /** @test */
    public function shop_billing_data_city_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['city' => 'Custom']]]], 'custom.*.city');
    }

    /** @test */
    public function shop_billing_data_postcode_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['postcode' => '12345']]]], 'custom.*.postcode');
    }

    /** @test */
    public function shop_billing_data_tax_id_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['tax_id' => '12345']]]], 'custom.*.tax_id');
    }

    /** @test */
    public function shop_billing_data_street_address_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['street_address' => 'Custom street address']]]], 'custom.*.street_address');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): ShopBillingDataFixture
    {
        return new ShopBillingDataFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
