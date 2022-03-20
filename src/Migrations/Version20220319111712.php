<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220319111712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add log tables; add model region project date fields';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        if (!$schema->hasTable('ozg_statistics_log_path_info')) {
            $this->addSql('CREATE TABLE ozg_statistics_log_path_info (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(1024) NOT NULL, route VARCHAR(255) DEFAULT NULL, path_type INT NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_PATH_TYPE (path_type), UNIQUE INDEX route_idx (route), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE ozg_statistics_log_search (id INT AUTO_INCREMENT NOT NULL, path_info_id INT DEFAULT NULL, search_term VARCHAR(255) DEFAULT NULL, search_count INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_CD5AE5C09A3DF7D1 (path_info_id), INDEX IDX_PATH_SEARCH (path_info_id, search_term), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE ozg_statistics_log_summary (id INT AUTO_INCREMENT NOT NULL, path_info_id INT DEFAULT NULL, entry_date LONGTEXT DEFAULT NULL, access_count INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_1D8738A69A3DF7D1 (path_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE ozg_statistics_log_search ADD CONSTRAINT FK_CD5AE5C09A3DF7D1 FOREIGN KEY (path_info_id) REFERENCES ozg_statistics_log_path_info (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE ozg_statistics_log_summary ADD CONSTRAINT FK_1D8738A69A3DF7D1 FOREIGN KEY (path_info_id) REFERENCES ozg_statistics_log_path_info (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE mb_user_user CHANGE facebook_data facebook_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE twitter_data twitter_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE gplus_data gplus_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
            $this->addSql('ALTER TABLE ozg_api_service_base_result CHANGE special_features special_features LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE synonyms synonyms LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE legal_basis_uris legal_basis_uris LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
            $this->addSql('ALTER TABLE ozg_implementation_project CHANGE efa_type efa_type INT DEFAULT NULL');
            $this->addSql('ALTER TABLE ozg_onboarding_epayment CHANGE xfinance_file_days xfinance_file_days LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
            $this->addSql('ALTER TABLE ozg_search CHANGE parameters parameters LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        }
        if (!$schema->hasTable('ozg_statistics_log_entry')) {
            $this->addSql('CREATE TABLE ozg_statistics_log_entry (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, request_method VARCHAR(255) DEFAULT NULL, request_locale VARCHAR(255) DEFAULT NULL, path_info VARCHAR(1024) DEFAULT NULL, route VARCHAR(255) DEFAULT NULL, request_attributes JSON DEFAULT NULL, query_parameters JSON DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_5DA75E79A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE ozg_statistics_log_entry ADD CONSTRAINT FK_5DA75E79A76ED395 FOREIGN KEY (user_id) REFERENCES mb_user_user (id)');
            $this->addSql('ALTER TABLE ozg_statistics_log_entry ADD title VARCHAR(255) DEFAULT NULL, ADD title_prefix VARCHAR(255) DEFAULT NULL');
        }
        $this->addSql('ALTER TABLE ozg_statistics_log_entry CHANGE request_attributes request_attributes LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE query_parameters query_parameters LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE ozg_model_region_project ADD project_concept_start_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD project_implementation_start_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE ozg_model_region_project ADD project_lead LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_statistics_log_search DROP FOREIGN KEY FK_CD5AE5C09A3DF7D1');
        $this->addSql('ALTER TABLE ozg_statistics_log_summary DROP FOREIGN KEY FK_1D8738A69A3DF7D1');
        $this->addSql('DROP TABLE ozg_statistics_log_path_info');
        $this->addSql('DROP TABLE ozg_statistics_log_search');
        $this->addSql('DROP TABLE ozg_statistics_log_summary');
        $this->addSql('ALTER TABLE mb_user_user CHANGE facebook_data facebook_data LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE twitter_data twitter_data LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE gplus_data gplus_data LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE ozg_api_service_base_result CHANGE special_features special_features LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE synonyms synonyms LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE legal_basis_uris legal_basis_uris LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE ozg_implementation_project CHANGE efa_type efa_type INT NOT NULL');
        $this->addSql('ALTER TABLE ozg_model_region_project DROP project_concept_start_at, DROP project_implementation_start_at, DROP project_lead');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment CHANGE xfinance_file_days xfinance_file_days LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE ozg_search CHANGE parameters parameters LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE ozg_statistics_log_entry CHANGE request_attributes request_attributes LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE query_parameters query_parameters LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`');
    }
}
