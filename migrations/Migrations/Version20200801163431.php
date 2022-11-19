<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200801163431 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_model_region_project_solutions (model_region_project_id INT NOT NULL, solution_id INT NOT NULL, INDEX IDX_3D68CC4A127D04D2 (model_region_project_id), INDEX IDX_3D68CC4A1C0BE183 (solution_id), PRIMARY KEY(model_region_project_id, solution_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_efile (id INT AUTO_INCREMENT NOT NULL, service_provider_id INT DEFAULT NULL, status_id INT DEFAULT NULL, leading_system_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, has_economic_viability_assessment TINYINT(1) DEFAULT NULL, sum_investments NUMERIC(12, 2) DEFAULT NULL, follow_up_costs NUMERIC(12, 2) DEFAULT NULL, saving_potential_notes LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, url VARCHAR(2048) DEFAULT NULL, INDEX IDX_17830EFBC6C98E06 (service_provider_id), INDEX IDX_17830EFB6BF700BD (status_id), INDEX IDX_17830EFB4DD70A9F (leading_system_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_efile_specialized_procedures (efile_id INT NOT NULL, specialized_procedure_id INT NOT NULL, INDEX IDX_11F56D0FF38ED7A4 (efile_id), INDEX IDX_11F56D0F452D2882 (specialized_procedure_id), PRIMARY KEY(efile_id, specialized_procedure_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_efile_storage_type_mm (efile_id INT NOT NULL, storage_type_id INT NOT NULL, INDEX IDX_D04E2223F38ED7A4 (efile_id), INDEX IDX_D04E2223B270BFF1 (storage_type_id), PRIMARY KEY(efile_id, storage_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_efile_software_modules (efile_id INT NOT NULL, specialized_procedure_id INT NOT NULL, INDEX IDX_A6984BB3F38ED7A4 (efile_id), INDEX IDX_A6984BB3452D2882 (specialized_procedure_id), PRIMARY KEY(efile_id, specialized_procedure_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_efile_status (id INT AUTO_INCREMENT NOT NULL, level INT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_solution_service_provider (solution_id INT NOT NULL, service_provider_id INT NOT NULL, INDEX IDX_5C7F742D1C0BE183 (solution_id), INDEX IDX_5C7F742DC6C98E06 (service_provider_id), PRIMARY KEY(solution_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_efile_storage_type (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_model_region_project_solutions ADD CONSTRAINT FK_3D68CC4A127D04D2 FOREIGN KEY (model_region_project_id) REFERENCES ozg_model_region_project (id)');
        $this->addSql('ALTER TABLE ozg_model_region_project_solutions ADD CONSTRAINT FK_3D68CC4A1C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('ALTER TABLE ozg_efile ADD CONSTRAINT FK_17830EFBC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id)');
        $this->addSql('ALTER TABLE ozg_efile ADD CONSTRAINT FK_17830EFB6BF700BD FOREIGN KEY (status_id) REFERENCES ozg_efile_status (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_efile ADD CONSTRAINT FK_17830EFB4DD70A9F FOREIGN KEY (leading_system_id) REFERENCES ozg_specialized_procedure (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_efile_specialized_procedures ADD CONSTRAINT FK_11F56D0FF38ED7A4 FOREIGN KEY (efile_id) REFERENCES ozg_efile (id)');
        $this->addSql('ALTER TABLE ozg_efile_specialized_procedures ADD CONSTRAINT FK_11F56D0F452D2882 FOREIGN KEY (specialized_procedure_id) REFERENCES ozg_specialized_procedure (id)');
        $this->addSql('ALTER TABLE ozg_efile_storage_type_mm ADD CONSTRAINT FK_D04E2223F38ED7A4 FOREIGN KEY (efile_id) REFERENCES ozg_efile (id)');
        $this->addSql('ALTER TABLE ozg_efile_storage_type_mm ADD CONSTRAINT FK_D04E2223B270BFF1 FOREIGN KEY (storage_type_id) REFERENCES ozg_efile_storage_type (id)');
        $this->addSql('ALTER TABLE ozg_efile_software_modules ADD CONSTRAINT FK_A6984BB3F38ED7A4 FOREIGN KEY (efile_id) REFERENCES ozg_efile (id)');
        $this->addSql('ALTER TABLE ozg_efile_software_modules ADD CONSTRAINT FK_A6984BB3452D2882 FOREIGN KEY (specialized_procedure_id) REFERENCES ozg_specialized_procedure (id)');
        $this->addSql('ALTER TABLE ozg_solution_service_provider ADD CONSTRAINT FK_5C7F742D1C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('ALTER TABLE ozg_solution_service_provider ADD CONSTRAINT FK_5C7F742DC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id)');
        $this->addSql('ALTER TABLE ozg_service CHANGE relevance1 relevance1 TINYINT(1) DEFAULT NULL, CHANGE relevance2 relevance2 TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions CHANGE usable_as_print_template usable_as_print_template TINYINT(1) DEFAULT NULL');
        $this->addSql('REPLACE INTO ozg_solution_service_provider (solution_id, service_provider_id) SELECT id, service_provider_id FROM ozg_solution WHERE service_provider_id IS NOT NULL');
        $this->addSql('ALTER TABLE ozg_solution DROP FOREIGN KEY FK_595F587DC6C98E06');
        $this->addSql('DROP INDEX IDX_595F587DC6C98E06 ON ozg_solution');
        $this->addSql('ALTER TABLE ozg_solution DROP service_provider_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_efile_specialized_procedures DROP FOREIGN KEY FK_11F56D0FF38ED7A4');
        $this->addSql('ALTER TABLE ozg_efile_storage_type_mm DROP FOREIGN KEY FK_D04E2223F38ED7A4');
        $this->addSql('ALTER TABLE ozg_efile_software_modules DROP FOREIGN KEY FK_A6984BB3F38ED7A4');
        $this->addSql('ALTER TABLE ozg_efile DROP FOREIGN KEY FK_17830EFB6BF700BD');
        $this->addSql('ALTER TABLE ozg_efile_storage_type_mm DROP FOREIGN KEY FK_D04E2223B270BFF1');
        $this->addSql('DROP TABLE ozg_model_region_project_solutions');
        $this->addSql('DROP TABLE ozg_efile');
        $this->addSql('DROP TABLE ozg_efile_specialized_procedures');
        $this->addSql('DROP TABLE ozg_efile_storage_type_mm');
        $this->addSql('DROP TABLE ozg_efile_software_modules');
        $this->addSql('DROP TABLE ozg_efile_status');
        $this->addSql('DROP TABLE ozg_solution_service_provider');
        $this->addSql('DROP TABLE ozg_efile_storage_type');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions CHANGE usable_as_print_template usable_as_print_template TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE ozg_service CHANGE relevance1 relevance1 TINYINT(1) NOT NULL, CHANGE relevance2 relevance2 TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE ozg_solution ADD service_provider_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_solution ADD CONSTRAINT FK_595F587DC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id)');
        $this->addSql('CREATE INDEX IDX_595F587DC6C98E06 ON ozg_solution (service_provider_id)');
    }
}
