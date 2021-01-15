<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210115115150 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_efile_commune (efile_id INT NOT NULL, commune_id INT NOT NULL, INDEX IDX_2E706E36F38ED7A4 (efile_id), INDEX IDX_2E706E36131A4F72 (commune_id), PRIMARY KEY(efile_id, commune_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_efile_commune ADD CONSTRAINT FK_2E706E36F38ED7A4 FOREIGN KEY (efile_id) REFERENCES ozg_efile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_efile_commune ADD CONSTRAINT FK_2E706E36131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_efile_commune');
    }
}
