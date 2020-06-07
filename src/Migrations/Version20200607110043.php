<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200607110043 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_model_region_project (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, project_start_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', project_end_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', usp LONGTEXT DEFAULT NULL, transferable_service LONGTEXT DEFAULT NULL, transferable_start LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_model_region_project_organisation (model_region_project_id INT NOT NULL, organisation_id INT NOT NULL, INDEX IDX_4A57EAD9127D04D2 (model_region_project_id), INDEX IDX_4A57EAD99E6B1585 (organisation_id), PRIMARY KEY(model_region_project_id, organisation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_model_region (id INT AUTO_INCREMENT NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, url VARCHAR(2048) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_model_region_project_list (model_region_id INT NOT NULL, model_region_project_id INT NOT NULL, INDEX IDX_E6104C47A1EF68C6 (model_region_id), INDEX IDX_E6104C47127D04D2 (model_region_project_id), PRIMARY KEY(model_region_id, model_region_project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_model_region_beneficiary (id INT AUTO_INCREMENT NOT NULL, organisation_id INT DEFAULT NULL, short_name VARCHAR(255) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, url VARCHAR(2048) DEFAULT NULL, UNIQUE INDEX UNIQ_5701B3AA9E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_model_region_project_organisation ADD CONSTRAINT FK_4A57EAD9127D04D2 FOREIGN KEY (model_region_project_id) REFERENCES ozg_model_region_project (id)');
        $this->addSql('ALTER TABLE ozg_model_region_project_organisation ADD CONSTRAINT FK_4A57EAD99E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('ALTER TABLE ozg_model_region_project_list ADD CONSTRAINT FK_E6104C47A1EF68C6 FOREIGN KEY (model_region_id) REFERENCES ozg_model_region (id)');
        $this->addSql('ALTER TABLE ozg_model_region_project_list ADD CONSTRAINT FK_E6104C47127D04D2 FOREIGN KEY (model_region_project_id) REFERENCES ozg_model_region_project (id)');
        $this->addSql('ALTER TABLE ozg_model_region_beneficiary ADD CONSTRAINT FK_5701B3AA9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_organisation ADD model_region_beneficiary_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_organisation ADD CONSTRAINT FK_609D3524CADD1A77 FOREIGN KEY (model_region_beneficiary_id) REFERENCES ozg_model_region_beneficiary (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_609D3524CADD1A77 ON ozg_organisation (model_region_beneficiary_id)');
        $this->addSql('ALTER TABLE ozg_service_provider DROP FOREIGN KEY FK_54ECCF6F9E6B1585');
        $this->addSql('DROP INDEX UNIQ_54ECCF6F9E6B1585 ON ozg_service_provider');
        $this->addSql('ALTER TABLE ozg_service_provider DROP organisation_id');
        $this->addSql('ALTER TABLE ozg_model_region_project ADD communes_benefits LONGTEXT DEFAULT NULL, ADD import_id INT DEFAULT NULL, ADD import_source VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_model_region_project_organisation DROP FOREIGN KEY FK_4A57EAD9127D04D2');
        $this->addSql('ALTER TABLE ozg_model_region_project_list DROP FOREIGN KEY FK_E6104C47127D04D2');
        $this->addSql('ALTER TABLE ozg_model_region_project_list DROP FOREIGN KEY FK_E6104C47A1EF68C6');
        $this->addSql('ALTER TABLE ozg_organisation DROP FOREIGN KEY FK_609D3524CADD1A77');
        $this->addSql('DROP TABLE ozg_model_region_project');
        $this->addSql('DROP TABLE ozg_model_region_project_organisation');
        $this->addSql('DROP TABLE ozg_model_region');
        $this->addSql('DROP TABLE ozg_model_region_project_list');
        $this->addSql('DROP TABLE ozg_model_region_beneficiary');
        $this->addSql('DROP INDEX UNIQ_609D3524CADD1A77 ON ozg_organisation');
        $this->addSql('ALTER TABLE ozg_organisation DROP model_region_beneficiary_id');
        $this->addSql('ALTER TABLE ozg_service_provider ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service_provider ADD CONSTRAINT FK_54ECCF6F9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_54ECCF6F9E6B1585 ON ozg_service_provider (organisation_id)');
        $this->addSql('ALTER TABLE ozg_model_region_project DROP communes_benefits, DROP import_id, DROP import_source');
    }
}
