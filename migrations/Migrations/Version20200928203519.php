<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200928203519 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_commune_laboratory (commune_id INT NOT NULL, laboratory_id INT NOT NULL, INDEX IDX_E53EBC32131A4F72 (commune_id), INDEX IDX_E53EBC322F2A371E (laboratory_id), PRIMARY KEY(commune_id, laboratory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_commune_laboratory ADD CONSTRAINT FK_E53EBC32131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id)');
        $this->addSql('ALTER TABLE ozg_commune_laboratory ADD CONSTRAINT FK_E53EBC322F2A371E FOREIGN KEY (laboratory_id) REFERENCES ozg_laboratory (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_commune_laboratory');
    }
}
