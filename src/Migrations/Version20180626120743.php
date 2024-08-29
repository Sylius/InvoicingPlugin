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

final class Version20180626120743 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        // this up() migration is auto-generated, please modify it to your needs

        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_invoice (id VARCHAR(255) NOT NULL, billing_data_id INT DEFAULT NULL, order_number VARCHAR(255) NOT NULL, issued_at DATETIME NOT NULL, currency_code VARCHAR(3) NOT NULL, tax_total INT NOT NULL, total INT NOT NULL, UNIQUE INDEX UNIQ_3AA279BF5CDB2AEB (billing_data_id), INDEX IDX_3AA279BF551F0F81 (order_number), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_line_item (id INT AUTO_INCREMENT NOT NULL, invoice_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, unit_price INT NOT NULL, subtotal INT NOT NULL, tax_total INT NOT NULL, total INT NOT NULL, variant_code VARCHAR(255) DEFAULT NULL, variant_name VARCHAR(255) DEFAULT NULL, INDEX IDX_C91408292989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_billing_data (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL, province_code VARCHAR(255) DEFAULT NULL, province_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF5CDB2AEB FOREIGN KEY (billing_data_id) REFERENCES sylius_invoicing_plugin_billing_data (id)');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD CONSTRAINT FK_C91408292989F1FD FOREIGN KEY (invoice_id) REFERENCES sylius_invoicing_plugin_invoice (id) ON DELETE CASCADE');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_invoice (id VARCHAR(255) NOT NULL, billing_data_id INT DEFAULT NULL, order_number VARCHAR(255) NOT NULL, issued_at TIMESTAMP NOT NULL, currency_code VARCHAR(3) NOT NULL, tax_total INT NOT NULL, total INT NOT NULL, PRIMARY KEY (id), UNIQUE (billing_data_id))');
            $this->addSql('CREATE INDEX IDX_3AA279BF551F0F81 ON sylius_invoicing_plugin_invoice (order_number)');
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_line_item (id SERIAL PRIMARY KEY, invoice_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, unit_price INT NOT NULL, subtotal INT NOT NULL, tax_total INT NOT NULL, total INT NOT NULL, variant_code VARCHAR(255) DEFAULT NULL, variant_name VARCHAR(255) DEFAULT NULL)');
            $this->addSql('CREATE INDEX IDX_C91408292989F1FD ON sylius_invoicing_plugin_line_item (invoice_id)');
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_billing_data ( id SERIAL PRIMARY KEY, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL, province_code VARCHAR(255) DEFAULT NULL, province_name VARCHAR(255) DEFAULT NULL )');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF5CDB2AEB FOREIGN KEY (billing_data_id) REFERENCES sylius_invoicing_plugin_billing_data (id)');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD CONSTRAINT FK_C91408292989F1FD FOREIGN KEY (invoice_id) REFERENCES sylius_invoicing_plugin_invoice (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP FOREIGN KEY FK_C91408292989F1FD');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BF5CDB2AEB');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_invoice');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_line_item');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_billing_data');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP CONSTRAINT FK_C91408292989F1FD');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP CONSTRAINT FK_3AA279BF5CDB2AEB');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_invoice');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_line_item');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_billing_data');
        }

    }
}
