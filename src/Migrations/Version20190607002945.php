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

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190607002945 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('DROP TABLE sylius_invoicing_plugin_shop_billing_data');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice CHANGE shopBillingData_company shop_billing_data_company VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_tax_id shop_billing_data_tax_id VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_street shop_billing_data_street VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_city shop_billing_data_city VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_postcode shop_billing_data_postcode VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_country_code shop_billing_data_country_code VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_representative shop_billing_data_representative VARCHAR(255) DEFAULT NULL, CHANGE channel_code channel_code VARCHAR(255) NOT NULL, CHANGE channel_name channel_name VARCHAR(255) NOT NULL');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('DROP TABLE sylius_invoicing_plugin_shop_billing_data');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shopBillingData_company TO shop_billing_data_company');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shopBillingData_tax_id TO shop_billing_data_tax_id');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shopBillingData_street TO shop_billing_data_street');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shopBillingData_city TO shop_billing_data_city');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shopBillingData_postcode TO shop_billing_data_postcode');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shopBillingData_country_code TO shop_billing_data_country_code');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shopBillingData_representative TO shop_billing_data_representative');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN shop_billing_data_company TYPE VARCHAR(255), ALTER COLUMN shop_billing_data_company DROP NOT NULL, ALTER COLUMN shop_billing_data_tax_id TYPE VARCHAR(255), ALTER COLUMN shop_billing_data_tax_id DROP NOT NULL, ALTER COLUMN shop_billing_data_street TYPE VARCHAR(255), ALTER COLUMN shop_billing_data_street DROP NOT NULL, ALTER COLUMN shop_billing_data_city TYPE VARCHAR(255), ALTER COLUMN shop_billing_data_city DROP NOT NULL, ALTER COLUMN shop_billing_data_postcode TYPE VARCHAR(255), ALTER COLUMN shop_billing_data_postcode DROP NOT NULL, ALTER COLUMN shop_billing_data_country_code TYPE VARCHAR(255), ALTER COLUMN shop_billing_data_country_code DROP NOT NULL, ALTER COLUMN shop_billing_data_representative TYPE VARCHAR(255), ALTER COLUMN shop_billing_data_representative DROP NOT NULL, ALTER COLUMN channel_code TYPE VARCHAR(255), ALTER COLUMN channel_code SET NOT NULL, ALTER COLUMN channel_name TYPE VARCHAR(255), ALTER COLUMN channel_name SET NOT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_shop_billing_data (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, tax_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, street VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, city VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, postcode VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, country_code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice CHANGE shop_billing_data_company shopBillingData_company VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_tax_id shopBillingData_tax_id VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_street shopBillingData_street VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_city shopBillingData_city VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_postcode shopBillingData_postcode VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_country_code shopBillingData_country_code VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_representative shopBillingData_representative VARCHAR(255) DEFAULT NULL, CHANGE channel_code channel_code VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE channel_name channel_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_shop_billing_data ( id SERIAL PRIMARY KEY, company VARCHAR(255) NOT NULL, tax_id VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL )');
            $this->addSql('COMMENT ON TABLE sylius_invoicing_plugin_shop_billing_data IS \'\'');

            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shop_billing_data_company TO shopBillingData_company');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shop_billing_data_tax_id TO shopBillingData_tax_id');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shop_billing_data_street TO shopBillingData_street');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shop_billing_data_city TO shopBillingData_city');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shop_billing_data_postcode TO shopBillingData_postcode');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shop_billing_data_country_code TO shopBillingData_country_code');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice RENAME COLUMN shop_billing_data_representative TO shopBillingData_representative');

            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN shopBillingData_company SET DEFAULT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN shopBillingData_tax_id SET DEFAULT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN shopBillingData_street SET DEFAULT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN shopBillingData_city SET DEFAULT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN shopBillingData_postcode SET DEFAULT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN shopBillingData_country_code SET DEFAULT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN shopBillingData_representative SET DEFAULT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN channel_code SET DEFAULT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN channel_name SET DEFAULT NULL');

            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN channel_code TYPE VARCHAR(255) COLLATE "utf8_unicode_ci"');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ALTER COLUMN channel_name TYPE VARCHAR(255) COLLATE "utf8_unicode_ci"');
        }
    }
}
