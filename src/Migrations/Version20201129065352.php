<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201129065352 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_statistics_log_entry (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, request_method VARCHAR(255) DEFAULT NULL, request_locale VARCHAR(255) DEFAULT NULL, path_info VARCHAR(1024) DEFAULT NULL, route VARCHAR(255) DEFAULT NULL, request_attributes JSON DEFAULT NULL, query_parameters JSON DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_5DA75E79A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_statistics_log_entry ADD CONSTRAINT FK_5DA75E79A76ED395 FOREIGN KEY (user_id) REFERENCES mb_user_user (id)');
        $this->addSql('ALTER TABLE ozg_statistics_log_entry ADD title VARCHAR(255) DEFAULT NULL, ADD title_prefix VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_statistics_log_entry');
    }
}
