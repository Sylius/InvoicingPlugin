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
final class Version20220811054008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'original_unit_net_price field added';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD discounted_unit_net_price INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP discounted_unit_net_price');
    }
}
