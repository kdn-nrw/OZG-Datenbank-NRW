<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210331131510 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_configuration_custom_field (id INT AUTO_INCREMENT NOT NULL, field_label VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, record_type VARCHAR(255) DEFAULT NULL, field_type VARCHAR(255) DEFAULT NULL, field_options LONGTEXT DEFAULT NULL, required TINYINT(1) NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, position INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_configuration_custom_value (id INT AUTO_INCREMENT NOT NULL, custom_field_id INT DEFAULT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, onboarding_id INT DEFAULT NULL, value LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', record_type VARCHAR(255) NOT NULL, INDEX IDX_A669284EA1E5E0D4 (custom_field_id), INDEX IDX_A669284EDE12AB56 (created_by), INDEX IDX_A669284E25F94802 (modified_by), INDEX IDX_A669284E235CA921 (onboarding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding (id INT AUTO_INCREMENT NOT NULL, commune_id INT DEFAULT NULL, service_provider_id INT DEFAULT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, status INT NOT NULL, completion_rate INT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) NOT NULL, record_type VARCHAR(255) NOT NULL, INDEX IDX_FCF827A7131A4F72 (commune_id), UNIQUE INDEX UNIQ_FCF827A7C6C98E06 (service_provider_id), INDEX IDX_FCF827A7DE12AB56 (created_by), INDEX IDX_FCF827A725F94802 (modified_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding_base_info (id INT NOT NULL, privacy_text LONGTEXT DEFAULT NULL, privacy_url VARCHAR(2048) DEFAULT NULL, imprint_text LONGTEXT DEFAULT NULL, imprint_url VARCHAR(2048) DEFAULT NULL, accessibility LONGTEXT DEFAULT NULL, opening_hours LONGTEXT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding_contact (id INT AUTO_INCREMENT NOT NULL, commune_info_id INT DEFAULT NULL, epayment_id INT DEFAULT NULL, contact_type VARCHAR(50) DEFAULT NULL, external_user_name VARCHAR(255) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', gender INT DEFAULT NULL, title VARCHAR(100) DEFAULT NULL, first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(100) DEFAULT NULL, mobile_number VARCHAR(100) DEFAULT NULL, hidden TINYINT(1) NOT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, INDEX IDX_E6BDC8EABBB53111 (commune_info_id), UNIQUE INDEX UNIQ_E6BDC8EAAD4D405B (epayment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding_epayment (id INT NOT NULL, payment_provider_account_id VARCHAR(255) DEFAULT NULL, payment_provider VARCHAR(255) DEFAULT NULL, mandator_email VARCHAR(255) DEFAULT NULL, test_ip_address VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding_inquiry (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, user_id INT DEFAULT NULL, is_read TINYINT(1) NOT NULL, reference_id INT DEFAULT NULL, reference_source VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, read_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) NOT NULL, INDEX IDX_F0E62D22DE12AB56 (created_by), INDEX IDX_F0E62D2225F94802 (modified_by), INDEX IDX_F0E62D22A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding_service_account (id INT AUTO_INCREMENT NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding_service_dataclearing (id INT AUTO_INCREMENT NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_configuration_custom_value ADD CONSTRAINT FK_A669284EA1E5E0D4 FOREIGN KEY (custom_field_id) REFERENCES ozg_configuration_custom_field (id)');
        $this->addSql('ALTER TABLE ozg_configuration_custom_value ADD CONSTRAINT FK_A669284EDE12AB56 FOREIGN KEY (created_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_configuration_custom_value ADD CONSTRAINT FK_A669284E25F94802 FOREIGN KEY (modified_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_configuration_custom_value ADD CONSTRAINT FK_A669284E235CA921 FOREIGN KEY (onboarding_id) REFERENCES ozg_onboarding (id)');
        $this->addSql('ALTER TABLE ozg_onboarding ADD CONSTRAINT FK_FCF827A7131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding ADD CONSTRAINT FK_FCF827A7C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding ADD CONSTRAINT FK_FCF827A7DE12AB56 FOREIGN KEY (created_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_onboarding ADD CONSTRAINT FK_FCF827A725F94802 FOREIGN KEY (modified_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_base_info ADD CONSTRAINT FK_3232FF2ABF396750 FOREIGN KEY (id) REFERENCES ozg_onboarding (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding_contact ADD CONSTRAINT FK_E6BDC8EABBB53111 FOREIGN KEY (commune_info_id) REFERENCES ozg_onboarding_base_info (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding_contact ADD CONSTRAINT FK_E6BDC8EAAD4D405B FOREIGN KEY (epayment_id) REFERENCES ozg_onboarding_epayment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment ADD CONSTRAINT FK_651A4613BF396750 FOREIGN KEY (id) REFERENCES ozg_onboarding (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding_inquiry ADD CONSTRAINT FK_F0E62D22DE12AB56 FOREIGN KEY (created_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_inquiry ADD CONSTRAINT FK_F0E62D2225F94802 FOREIGN KEY (modified_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_inquiry ADD CONSTRAINT FK_F0E62D22A76ED395 FOREIGN KEY (user_id) REFERENCES mb_user_user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_configuration_custom_value DROP FOREIGN KEY FK_A669284EA1E5E0D4');
        $this->addSql('ALTER TABLE ozg_configuration_custom_value DROP FOREIGN KEY FK_A669284E235CA921');
        $this->addSql('ALTER TABLE ozg_onboarding_base_info DROP FOREIGN KEY FK_3232FF2ABF396750');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment DROP FOREIGN KEY FK_651A4613BF396750');
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP FOREIGN KEY FK_E6BDC8EABBB53111');
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP FOREIGN KEY FK_E6BDC8EAAD4D405B');
        $this->addSql('DROP TABLE ozg_configuration_custom_field');
        $this->addSql('DROP TABLE ozg_configuration_custom_value');
        $this->addSql('DROP TABLE ozg_onboarding');
        $this->addSql('DROP TABLE ozg_onboarding_base_info');
        $this->addSql('DROP TABLE ozg_onboarding_contact');
        $this->addSql('DROP TABLE ozg_onboarding_epayment');
        $this->addSql('DROP TABLE ozg_onboarding_inquiry');
        $this->addSql('DROP TABLE ozg_onboarding_service_account');
        $this->addSql('DROP TABLE ozg_onboarding_service_dataclearing');
    }
}
