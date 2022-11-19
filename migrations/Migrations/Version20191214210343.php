<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191214210343 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_solution_contact (solution_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_D79E334C1C0BE183 (solution_id), INDEX IDX_D79E334CE7A1254A (contact_id), PRIMARY KEY(solution_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_implementation_project_contact (implementation_project_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_BCAAF7F92CC68C60 (implementation_project_id), INDEX IDX_BCAAF7F9E7A1254A (contact_id), PRIMARY KEY(implementation_project_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_solution_contact ADD CONSTRAINT FK_D79E334C1C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('ALTER TABLE ozg_solution_contact ADD CONSTRAINT FK_D79E334CE7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact ADD CONSTRAINT FK_BCAAF7F92CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact ADD CONSTRAINT FK_BCAAF7F9E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id)');
        $this->addSql('ALTER TABLE ozg_ministry_state ADD short_name VARCHAR(255) DEFAULT NULL, ADD street VARCHAR(255) DEFAULT NULL, ADD zip_code VARCHAR(20) DEFAULT NULL, ADD town VARCHAR(255) DEFAULT NULL, ADD url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_contact ADD ministry_state_id INT DEFAULT NULL, ADD service_provider_id INT DEFAULT NULL, ADD commune_id INT DEFAULT NULL, ADD gender INT DEFAULT NULL, ADD title VARCHAR(100) DEFAULT NULL, ADD department VARCHAR(255) DEFAULT NULL, ADD fax_number VARCHAR(100) DEFAULT NULL, ADD mobile_number VARCHAR(100) DEFAULT NULL, CHANGE zipcode zip_code VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF202803929A9 FOREIGN KEY (ministry_state_id) REFERENCES ozg_ministry_state (id)');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF202C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id)');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF202131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id)');
        $this->addSql('CREATE INDEX IDX_2CCAF202803929A9 ON ozg_contact (ministry_state_id)');
        $this->addSql('CREATE INDEX IDX_2CCAF202C6C98E06 ON ozg_contact (service_provider_id)');
        $this->addSql('CREATE INDEX IDX_2CCAF202131A4F72 ON ozg_contact (commune_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_solution_contact');
        $this->addSql('DROP TABLE ozg_implementation_project_contact');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF202803929A9');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF202C6C98E06');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF202131A4F72');
        $this->addSql('DROP INDEX IDX_2CCAF202803929A9 ON ozg_contact');
        $this->addSql('DROP INDEX IDX_2CCAF202C6C98E06 ON ozg_contact');
        $this->addSql('DROP INDEX IDX_2CCAF202131A4F72 ON ozg_contact');
        $this->addSql('ALTER TABLE ozg_contact DROP ministry_state_id, DROP service_provider_id, DROP commune_id, DROP gender, DROP title, DROP department, DROP fax_number, DROP mobile_number, CHANGE zip_code zipcode VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE ozg_ministry_state DROP short_name, DROP street, DROP zip_code, DROP town, DROP url');
    }
}
