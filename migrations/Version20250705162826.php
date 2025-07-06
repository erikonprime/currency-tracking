<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250705162826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX uniq_base_target ON exchange_pair (base_currency, target_currency)');


    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_base_target ON exchange_pair');

    }
}
