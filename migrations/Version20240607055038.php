<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607055038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE request (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `services` CHANGE `is_visible` `is_visible` tinyint(1) NOT NULL DEFAULT true AFTER `description` ');
        $this->addSql('ALTER TABLE `services` CHANGE `service_photo` `service_photo` varchar(255) COLLATE `utf8mb4_unicode_ci` NULL AFTER `name`');
        $this->addSql('ALTER TABLE `client` CHANGE `logo` `logo` varchar(255) COLLATE `utf8mb4_unicode_ci` NULL AFTER `name`');
        $this->addSql('ALTER TABLE `teams` CHANGE `team_photo` `team_photo` varchar(255) COLLATE `utf8mb4_unicode_ci` NULL AFTER `name`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE request');
    }
}
