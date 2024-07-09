<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240708114144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE teams ADD designation VARCHAR(255) NOT NULL AFTER `name`');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE teams DROP designation, CHANGE team_photo team_photo VARCHAR(255) DEFAULT NULL, CHANGE order_by order_by TEXT NOT NULL');
    }
}
