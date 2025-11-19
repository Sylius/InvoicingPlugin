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

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20180625120743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Regenerated Sylius Invoicing migrations from 1.X';
    }

    public function postUp(Schema $schema): void
    {
        $this->cleanMigrationsTable();
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('sylius_invoicing_plugin_invoice')) {
            return;
        }

        $this->addSql('CREATE TABLE sylius_invoicing_plugin_billing_data (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL, province_code VARCHAR(255) DEFAULT NULL, province_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_invoice (id VARCHAR(255) NOT NULL, billing_data_id INT DEFAULT NULL, shop_billing_data_id INT DEFAULT NULL, channel_id INT DEFAULT NULL, order_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, issued_at DATETIME NOT NULL, currency_code VARCHAR(3) NOT NULL, locale_code VARCHAR(255) NOT NULL, total INT NOT NULL, payment_state VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3AA279BF5CDB2AEB (billing_data_id), UNIQUE INDEX UNIQ_3AA279BFB5282EDF (shop_billing_data_id), INDEX IDX_3AA279BF72F5A1AA (channel_id), INDEX IDX_3AA279BF8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_line_item (id INT AUTO_INCREMENT NOT NULL, invoice_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, unit_price INT NOT NULL, discounted_unit_net_price INT DEFAULT NULL, subtotal INT NOT NULL, tax_total INT NOT NULL, total INT NOT NULL, variant_code VARCHAR(255) DEFAULT NULL, variant_name VARCHAR(255) DEFAULT NULL, tax_rate VARCHAR(255) DEFAULT NULL, INDEX IDX_C91408292989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_sequence (id INT AUTO_INCREMENT NOT NULL, idx INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_shop_billing_data (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postcode VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, representative VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_tax_item (id INT AUTO_INCREMENT NOT NULL, invoice_id VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, amount INT NOT NULL, INDEX IDX_2951C61C2989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF5CDB2AEB FOREIGN KEY (billing_data_id) REFERENCES sylius_invoicing_plugin_billing_data (id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BFB5282EDF FOREIGN KEY (shop_billing_data_id) REFERENCES sylius_invoicing_plugin_shop_billing_data (id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF8D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD CONSTRAINT FK_C91408292989F1FD FOREIGN KEY (invoice_id) REFERENCES sylius_invoicing_plugin_invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_tax_item ADD CONSTRAINT FK_2951C61C2989F1FD FOREIGN KEY (invoice_id) REFERENCES sylius_invoicing_plugin_invoice (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BF5CDB2AEB');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BFB5282EDF');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BF72F5A1AA');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BF8D9F6D38');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP FOREIGN KEY FK_C91408292989F1FD');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_tax_item DROP FOREIGN KEY FK_2951C61C2989F1FD');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_billing_data');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_invoice');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_line_item');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_sequence');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_shop_billing_data');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_tax_item');
    }

    private function cleanMigrationsTable(): void
    {
        $this->connection->executeStatement('DELETE FROM sylius_migrations WHERE version LIKE :version AND version NOT IN (:current)', [
            'version' => 'Sylius\\\\InvoicingPlugin\\\\Migrations\\\\Version%',
            'current' => [
                'Sylius\\InvoicingPlugin\\Migrations\\Version20241121125624',
                self::class,
            ],
        ], [
            'version' => Types::STRING,
            'current' => ArrayParameterType::STRING,
        ]);
    }
}
