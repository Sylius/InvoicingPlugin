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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190103134228 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD shopBillingData_company VARCHAR(255) DEFAULT NULL, ADD shopBillingData_tax_id VARCHAR(255) DEFAULT NULL, ADD shopBillingData_street VARCHAR(255) DEFAULT NULL, ADD shopBillingData_city VARCHAR(255) DEFAULT NULL, ADD shopBillingData_postcode VARCHAR(255) DEFAULT NULL, ADD shopBillingData_country_code VARCHAR(255) DEFAULT NULL');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD COLUMN shopBillingData_company VARCHAR(255) DEFAULT NULL, ADD COLUMN shopBillingData_tax_id VARCHAR(255) DEFAULT NULL, ADD COLUMN shopBillingData_street VARCHAR(255) DEFAULT NULL, ADD COLUMN shopBillingData_city VARCHAR(255) DEFAULT NULL, ADD COLUMN shopBillingData_postcode VARCHAR(255) DEFAULT NULL, ADD COLUMN shopBillingData_country_code VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP shopBillingData_company, DROP shopBillingData_tax_id, DROP shopBillingData_street, DROP shopBillingData_city, DROP shopBillingData_postcode, DROP shopBillingData_country_code');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP COLUMN shopBillingData_company, DROP COLUMN shopBillingData_tax_id, DROP COLUMN shopBillingData_street, DROP COLUMN shopBillingData_city, DROP COLUMN shopBillingData_postcode, DROP COLUMN shopBillingData_country_code');
        }
    }
}
