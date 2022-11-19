<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201110092726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_api_service_base_result (id INT AUTO_INCREMENT NOT NULL, service_id INT DEFAULT NULL, fim_type_id INT DEFAULT NULL, service_key LONGTEXT DEFAULT NULL, service_group LONGTEXT DEFAULT NULL, call_sign LONGTEXT DEFAULT NULL, performance LONGTEXT DEFAULT NULL, performance_detail LONGTEXT DEFAULT NULL, name2 LONGTEXT DEFAULT NULL, type LONGTEXT DEFAULT NULL, service_type LONGTEXT DEFAULT NULL, date LONGTEXT DEFAULT NULL, special_features JSON DEFAULT NULL, synonyms JSON DEFAULT NULL, short_text LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, legal_basis LONGTEXT DEFAULT NULL, legal_basis_uris JSON DEFAULT NULL, required_documents LONGTEXT DEFAULT NULL, requirements LONGTEXT DEFAULT NULL, costs LONGTEXT DEFAULT NULL, processing_time LONGTEXT DEFAULT NULL, process_flow LONGTEXT DEFAULT NULL, deadlines LONGTEXT DEFAULT NULL, forms LONGTEXT DEFAULT NULL, further_information LONGTEXT DEFAULT NULL, url_online_service VARCHAR(255) DEFAULT NULL, teaser LONGTEXT DEFAULT NULL, point_of_contact VARCHAR(255) DEFAULT NULL, technically_approved_at VARCHAR(255) DEFAULT NULL, technically_approved_by VARCHAR(255) DEFAULT NULL, hints LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, import_id INT DEFAULT NULL, import_source VARCHAR(100) DEFAULT NULL, INDEX IDX_A0EA59A6ED5CA9E6 (service_id), UNIQUE INDEX UNIQ_A0EA59A6871E1659 (fim_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_api_service_base_result ADD CONSTRAINT FK_A0EA59A6ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_api_service_base_result ADD CONSTRAINT FK_A0EA59A6871E1659 FOREIGN KEY (fim_type_id) REFERENCES ozg_service_fim (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_service_fim ADD service_base_result_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service_fim ADD CONSTRAINT FK_B9A2942CB7ED7A78 FOREIGN KEY (service_base_result_id) REFERENCES ozg_api_service_base_result (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B9A2942CB7ED7A78 ON ozg_service_fim (service_base_result_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_service_fim DROP FOREIGN KEY FK_B9A2942CB7ED7A78');
        $this->addSql('DROP TABLE ozg_api_service_base_result');
        $this->addSql('DROP INDEX UNIQ_B9A2942CB7ED7A78 ON ozg_service_fim');
        $this->addSql('ALTER TABLE ozg_service_fim DROP service_base_result_id');
    }
}
