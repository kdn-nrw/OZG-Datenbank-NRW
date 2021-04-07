<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210407075338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_onboarding_epayment_project (id INT AUTO_INCREMENT NOT NULL, epayment_id INT DEFAULT NULL, provider_type VARCHAR(50) DEFAULT NULL, project_environment VARCHAR(50) DEFAULT NULL, project_id VARCHAR(100) DEFAULT NULL, project_password VARCHAR(255) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) NOT NULL, INDEX IDX_7D8B9ED8AD4D405B (epayment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment_project ADD CONSTRAINT FK_7D8B9ED8AD4D405B FOREIGN KEY (epayment_id) REFERENCES ozg_onboarding_epayment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_configuration_custom_field ADD placeholder VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding ADD message_count INT DEFAULT NULL, CHANGE completion_rate completion_rate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment ADD client_number_integration VARCHAR(255) DEFAULT NULL, ADD client_number_production VARCHAR(255) DEFAULT NULL, ADD manager_number VARCHAR(255) DEFAULT NULL, ADD budget_office VARCHAR(255) DEFAULT NULL, ADD object_number VARCHAR(255) DEFAULT NULL, ADD cash_register_personal_account_number VARCHAR(255) DEFAULT NULL, ADD indicator_dunning_procedure VARCHAR(255) DEFAULT NULL, ADD booking_text VARCHAR(255) DEFAULT NULL, ADD description_of_the_booking_list VARCHAR(255) DEFAULT NULL, ADD manager_no VARCHAR(255) DEFAULT NULL, ADD application_name VARCHAR(255) DEFAULT NULL, ADD length_receipt_number VARCHAR(255) DEFAULT NULL, ADD cash_register_check_procedure_status TINYINT(1) DEFAULT NULL, ADD length_first_account_assignment_information VARCHAR(255) DEFAULT NULL, ADD length_second_account_assignment_information VARCHAR(255) DEFAULT NULL, ADD street VARCHAR(255) DEFAULT NULL, ADD zip_code VARCHAR(20) DEFAULT NULL, ADD town VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL, ADD phone_number VARCHAR(100) DEFAULT NULL, ADD mobile_number VARCHAR(100) DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_REFERENCE_KEY ON ozg_onboarding_inquiry (reference_id, reference_source)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_onboarding_epayment_project');
        $this->addSql('ALTER TABLE ozg_configuration_custom_field DROP placeholder');
        $this->addSql('ALTER TABLE ozg_onboarding DROP message_count, CHANGE completion_rate completion_rate INT NOT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment DROP client_number_integration, DROP client_number_production, DROP manager_number, DROP budget_office, DROP object_number, DROP cash_register_personal_account_number, DROP indicator_dunning_procedure, DROP booking_text, DROP description_of_the_booking_list, DROP manager_no, DROP application_name, DROP length_receipt_number, DROP cash_register_check_procedure_status, DROP length_first_account_assignment_information, DROP length_second_account_assignment_information, DROP street, DROP zip_code, DROP town, DROP email, DROP phone_number, DROP mobile_number');
        $this->addSql('DROP INDEX IDX_REFERENCE_KEY ON ozg_onboarding_inquiry');
    }
}
