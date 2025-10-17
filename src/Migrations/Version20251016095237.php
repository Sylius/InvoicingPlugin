<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;


/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251016095237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add pdf_sent column to sylius_invoicing_plugin_invoice table (MYSQL)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD pdf_sent TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP pdf_sent');
    }
}
