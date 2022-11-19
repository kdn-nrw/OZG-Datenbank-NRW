<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191220135238 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_mailing (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, text_plain LONGTEXT DEFAULT NULL, start_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', send_start_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', send_end_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', status INT NOT NULL, recipient_count INT DEFAULT NULL, sent_count INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) NOT NULL, INDEX IDX_5E712564DE12AB56 (created_by), INDEX IDX_5E71256425F94802 (modified_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_mailing_ministry_state (mailing_id INT NOT NULL, ministry_state_id INT NOT NULL, INDEX IDX_D90936923931AB76 (mailing_id), INDEX IDX_D9093692803929A9 (ministry_state_id), PRIMARY KEY(mailing_id, ministry_state_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_mailing_service_provider (mailing_id INT NOT NULL, service_provider_id INT NOT NULL, INDEX IDX_FCCF14E63931AB76 (mailing_id), INDEX IDX_FCCF14E6C6C98E06 (service_provider_id), PRIMARY KEY(mailing_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_mailing_commune (mailing_id INT NOT NULL, commune_id INT NOT NULL, INDEX IDX_18D327273931AB76 (mailing_id), INDEX IDX_18D32727131A4F72 (commune_id), PRIMARY KEY(mailing_id, commune_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_mailing_category (mailing_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_E40E52233931AB76 (mailing_id), INDEX IDX_E40E522312469DE2 (category_id), PRIMARY KEY(mailing_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_mailing_exclude_contact (mailing_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_525D4B923931AB76 (mailing_id), INDEX IDX_525D4B92E7A1254A (contact_id), PRIMARY KEY(mailing_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_mailing_contact (id INT AUTO_INCREMENT NOT NULL, mailing_id INT DEFAULT NULL, contact_id INT DEFAULT NULL, send_status INT NOT NULL, sent_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) NOT NULL, INDEX IDX_B65310F13931AB76 (mailing_id), INDEX IDX_B65310F1E7A1254A (contact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_mailing ADD CONSTRAINT FK_5E712564DE12AB56 FOREIGN KEY (created_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_mailing ADD CONSTRAINT FK_5E71256425F94802 FOREIGN KEY (modified_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_mailing_ministry_state ADD CONSTRAINT FK_D90936923931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_ministry_state ADD CONSTRAINT FK_D9093692803929A9 FOREIGN KEY (ministry_state_id) REFERENCES ozg_ministry_state (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_service_provider ADD CONSTRAINT FK_FCCF14E63931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_service_provider ADD CONSTRAINT FK_FCCF14E6C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_commune ADD CONSTRAINT FK_18D327273931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_commune ADD CONSTRAINT FK_18D32727131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_category ADD CONSTRAINT FK_E40E52233931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_category ADD CONSTRAINT FK_E40E522312469DE2 FOREIGN KEY (category_id) REFERENCES ozg_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_exclude_contact ADD CONSTRAINT FK_525D4B923931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_exclude_contact ADD CONSTRAINT FK_525D4B92E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_contact ADD CONSTRAINT FK_B65310F13931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_contact ADD CONSTRAINT FK_B65310F1E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_category ADD import_id INT DEFAULT NULL, ADD import_source VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_contact ADD import_id INT DEFAULT NULL, ADD import_source VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_mailing ADD greeting_type VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_mailing_contact ADD send_attempts INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_mailing_ministry_state DROP FOREIGN KEY FK_D90936923931AB76');
        $this->addSql('ALTER TABLE ozg_mailing_service_provider DROP FOREIGN KEY FK_FCCF14E63931AB76');
        $this->addSql('ALTER TABLE ozg_mailing_commune DROP FOREIGN KEY FK_18D327273931AB76');
        $this->addSql('ALTER TABLE ozg_mailing_category DROP FOREIGN KEY FK_E40E52233931AB76');
        $this->addSql('ALTER TABLE ozg_mailing_exclude_contact DROP FOREIGN KEY FK_525D4B923931AB76');
        $this->addSql('ALTER TABLE ozg_mailing_contact DROP FOREIGN KEY FK_B65310F13931AB76');
        $this->addSql('DROP TABLE ozg_mailing');
        $this->addSql('DROP TABLE ozg_mailing_ministry_state');
        $this->addSql('DROP TABLE ozg_mailing_service_provider');
        $this->addSql('DROP TABLE ozg_mailing_commune');
        $this->addSql('DROP TABLE ozg_mailing_category');
        $this->addSql('DROP TABLE ozg_mailing_exclude_contact');
        $this->addSql('DROP TABLE ozg_mailing_contact');
        $this->addSql('ALTER TABLE ozg_category DROP import_id, DROP import_source');
        $this->addSql('ALTER TABLE ozg_contact DROP import_id, DROP import_source');
    }
}
