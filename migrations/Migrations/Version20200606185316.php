<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200606185316 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE central_association (id INT AUTO_INCREMENT NOT NULL, organisation_id INT DEFAULT NULL, short_name VARCHAR(255) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, url VARCHAR(2048) DEFAULT NULL, UNIQUE INDEX UNIQ_DBD91FD19E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_commune_central_association (commune_id INT NOT NULL, central_association_id INT NOT NULL, INDEX IDX_78ACC165131A4F72 (commune_id), INDEX IDX_78ACC165D85FFE02 (central_association_id), PRIMARY KEY(commune_id, central_association_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE central_association ADD CONSTRAINT FK_DBD91FD19E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_commune_central_association ADD CONSTRAINT FK_78ACC165131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id)');
        $this->addSql('ALTER TABLE ozg_commune_central_association ADD CONSTRAINT FK_78ACC165D85FFE02 FOREIGN KEY (central_association_id) REFERENCES central_association (id)');
        $this->addSql('ALTER TABLE ozg_organisation ADD central_association_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_organisation ADD CONSTRAINT FK_609D3524D85FFE02 FOREIGN KEY (central_association_id) REFERENCES central_association (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_609D3524D85FFE02 ON ozg_organisation (central_association_id)');
        $this->addSql('ALTER TABLE ozg_service_provider ADD short_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_commune_central_association DROP FOREIGN KEY FK_78ACC165D85FFE02');
        $this->addSql('ALTER TABLE ozg_organisation DROP FOREIGN KEY FK_609D3524D85FFE02');
        $this->addSql('DROP TABLE central_association');
        $this->addSql('DROP TABLE ozg_commune_central_association');
        $this->addSql('DROP INDEX UNIQ_609D3524D85FFE02 ON ozg_organisation');
        $this->addSql('ALTER TABLE ozg_organisation DROP central_association_id');
        $this->addSql('ALTER TABLE ozg_service_provider DROP short_name');
    }
}
