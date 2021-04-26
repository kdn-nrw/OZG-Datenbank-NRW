<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210426094146 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable('ozg_email_template')) {
            $this->addSql('CREATE TABLE ozg_email_template (id INT AUTO_INCREMENT NOT NULL, template_key VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, sender_email VARCHAR(1024) DEFAULT NULL, sender_name VARCHAR(255) DEFAULT NULL, default_recipient VARCHAR(1024) DEFAULT NULL, reply_to_email VARCHAR(1024) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, body LONGTEXT DEFAULT NULL, cc_addresses LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) NOT NULL, INDEX IDX_TEMPLATE_KEY (template_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }
        if (!$schema->hasTable('ozg_onboarding_epayment_service')) {
            $this->addSql('CREATE TABLE ozg_onboarding_epayment_service (id INT AUTO_INCREMENT NOT NULL, service_id INT DEFAULT NULL, epayment_id INT DEFAULT NULL, booking_text LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, value_first_account_assignment_information LONGTEXT DEFAULT NULL, value_second_account_assignment_information LONGTEXT DEFAULT NULL, cost_unit LONGTEXT DEFAULT NULL, payers LONGTEXT DEFAULT NULL, product_description LONGTEXT DEFAULT NULL, tax_number LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) NOT NULL, position INT DEFAULT NULL, INDEX IDX_B3A5D4E4ED5CA9E6 (service_id), INDEX IDX_B3A5D4E4AD4D405B (epayment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE ozg_onboarding_service (id INT AUTO_INCREMENT NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, url VARCHAR(2048) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE ozg_onboarding_epayment_service ADD CONSTRAINT FK_B3A5D4E4ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_onboarding_service (id)');
            $this->addSql('ALTER TABLE ozg_onboarding_epayment_service ADD CONSTRAINT FK_B3A5D4E4AD4D405B FOREIGN KEY (epayment_id) REFERENCES ozg_onboarding_epayment (id)');
            $this->addSql('ALTER TABLE ozg_onboarding_contact ADD service_account_id INT DEFAULT NULL');
            $this->addSql('ALTER TABLE ozg_onboarding_contact ADD CONSTRAINT FK_E6BDC8EAD3A660E FOREIGN KEY (service_account_id) REFERENCES ozg_onboarding_service_account (id) ON DELETE CASCADE');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_E6BDC8EAD3A660E ON ozg_onboarding_contact (service_account_id)');
            $this->addSql('ALTER TABLE ozg_onboarding_epayment ADD content_first_account_assignment_information LONGTEXT DEFAULT NULL, ADD content_second_account_assignment_information LONGTEXT DEFAULT NULL');
        }
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');
        $accountTable = $schema->getTable('ozg_onboarding_service_account');
        if (!$accountTable->hasColumn('payment_operator_id')) {
            $this->addSql('ALTER TABLE ozg_onboarding_service_account ADD payment_operator_id INT DEFAULT NULL, ADD payment_provider VARCHAR(255) DEFAULT NULL, ADD mandator_email VARCHAR(255) DEFAULT NULL, ADD account_mandator_state INT DEFAULT NULL, ADD answer_url_1 VARCHAR(1024) DEFAULT NULL, ADD answer_url_2 VARCHAR(1024) DEFAULT NULL, ADD client_id VARCHAR(255) DEFAULT NULL, ADD client_password VARCHAR(255) DEFAULT NULL, ADD street VARCHAR(255) DEFAULT NULL, ADD zip_code VARCHAR(20) DEFAULT NULL, ADD town VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL, ADD phone_number VARCHAR(100) DEFAULT NULL, ADD mobile_number VARCHAR(100) DEFAULT NULL');
        }
        if ($accountTable->hasColumn('modified_at')) {
            $this->addSql('ALTER TABLE ozg_onboarding_service_account DROP modified_at, DROP created_at, DROP hidden');
        }
        if ($accountTable->hasColumn('name')) {
            $this->addSql('ALTER TABLE ozg_onboarding_service_account CHANGE name payment_provider_account_id VARCHAR(255) DEFAULT NULL');
        }
        $this->addSql('ALTER TABLE ozg_onboarding_service_account CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_service_account ADD CONSTRAINT FK_B0F71E55AEC94879 FOREIGN KEY (payment_operator_id) REFERENCES ozg_service_provider (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_service_account ADD CONSTRAINT FK_B0F71E55BF396750 FOREIGN KEY (id) REFERENCES ozg_onboarding (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B0F71E55AEC94879 ON ozg_onboarding_service_account (payment_operator_id)');
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_epayment_service DROP FOREIGN KEY FK_B3A5D4E4ED5CA9E6');
        $this->addSql('DROP TABLE ozg_email_template');
        $this->addSql('DROP TABLE ozg_onboarding_epayment_service');
        $this->addSql('DROP TABLE ozg_onboarding_service');
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP FOREIGN KEY FK_E6BDC8EAD3A660E');
        $this->addSql('DROP INDEX UNIQ_E6BDC8EAD3A660E ON ozg_onboarding_contact');
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP service_account_id');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment DROP content_first_account_assignment_information, DROP content_second_account_assignment_information');
        $this->addSql('ALTER TABLE ozg_onboarding_service_account DROP FOREIGN KEY FK_B0F71E55AEC94879');
        $this->addSql('ALTER TABLE ozg_onboarding_service_account DROP FOREIGN KEY FK_B0F71E55BF396750');
        $this->addSql('DROP INDEX IDX_B0F71E55AEC94879 ON ozg_onboarding_service_account');
        $this->addSql('ALTER TABLE ozg_onboarding_service_account ADD modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD hidden TINYINT(1) NOT NULL, DROP payment_operator_id, DROP payment_provider_account_id, DROP payment_provider, DROP mandator_email, DROP account_mandator_state, DROP answer_url_1, DROP answer_url_2, DROP client_id, DROP client_password, DROP street, DROP zip_code, DROP town, DROP email, DROP phone_number, DROP mobile_number, CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
