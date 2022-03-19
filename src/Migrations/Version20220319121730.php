<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220319121730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_bureau_commune (bureau_id INT NOT NULL, commune_id INT NOT NULL, INDEX IDX_6A9D472E32516FE2 (bureau_id), INDEX IDX_6A9D472E131A4F72 (commune_id), PRIMARY KEY(bureau_id, commune_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_bureau_commune ADD CONSTRAINT FK_6A9D472E32516FE2 FOREIGN KEY (bureau_id) REFERENCES ozg_bureau (id)');
        $this->addSql('ALTER TABLE ozg_bureau_commune ADD CONSTRAINT FK_6A9D472E131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_bureau_commune');
    }
}
