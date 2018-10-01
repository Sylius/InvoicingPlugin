<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180711070226 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_invoicing_plugin_tax_item (id INT AUTO_INCREMENT NOT NULL, invoice_id VARCHAR(255) NOT NULL, `label` VARCHAR(255) NOT NULL, amount INT NOT NULL, INDEX IDX_2951C61C2989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_shop_billing_data (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) NOT NULL, tax_id VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_tax_item ADD CONSTRAINT FK_2951C61C2989F1FD FOREIGN KEY (invoice_id) REFERENCES sylius_invoicing_plugin_invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP tax_total');
        $this->addSql('ALTER TABLE sylius_channel ADD billing_data_id INT DEFAULT NULL, ADD taxId VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E5CDB2AEB FOREIGN KEY (billing_data_id) REFERENCES sylius_invoicing_plugin_shop_billing_data (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16C8119E5CDB2AEB ON sylius_channel (billing_data_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E5CDB2AEB');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_tax_item');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_shop_billing_data');
        $this->addSql('DROP INDEX UNIQ_16C8119E5CDB2AEB ON sylius_channel');
        $this->addSql('ALTER TABLE sylius_channel DROP billing_data_id, DROP taxId');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD tax_total INT NOT NULL');
    }
}
