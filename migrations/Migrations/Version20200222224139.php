<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200222224139 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_mailing_organisation (mailing_id INT NOT NULL, organisation_id INT NOT NULL, INDEX IDX_6A1377F53931AB76 (mailing_id), INDEX IDX_6A1377F59E6B1585 (organisation_id), PRIMARY KEY(mailing_id, organisation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_mailing_organisation ADD CONSTRAINT FK_6A1377F53931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_organisation ADD CONSTRAINT FK_6A1377F59E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE ozg_mailing_commune');
        $this->addSql('DROP TABLE ozg_mailing_ministry_state');
        $this->addSql('DROP TABLE ozg_mailing_service_provider');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF202131A4F72');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF202803929A9');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF202A23B42D');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF202C6C98E06');
        $this->addSql('DROP INDEX IDX_2CCAF202C6C98E06 ON ozg_contact');
        $this->addSql('DROP INDEX IDX_2CCAF202A23B42D ON ozg_contact');
        $this->addSql('DROP INDEX IDX_2CCAF202803929A9 ON ozg_contact');
        $this->addSql('DROP INDEX IDX_2CCAF202131A4F72 ON ozg_contact');
        $this->addSql('ALTER TABLE ozg_contact DROP ministry_state_id, DROP service_provider_id, DROP commune_id, DROP manufacturer_id');
        $this->addSql('ALTER TABLE ozg_organisation DROP ref_entity_id');
        $this->addSql('CREATE TABLE ozg_implementation_project_organisation_interested (implementation_project_id INT NOT NULL, organisation_id INT NOT NULL, INDEX IDX_86C0C7C42CC68C60 (implementation_project_id), INDEX IDX_86C0C7C49E6B1585 (organisation_id), PRIMARY KEY(implementation_project_id, organisation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_implementation_project_organisation_participation (implementation_project_id INT NOT NULL, organisation_id INT NOT NULL, INDEX IDX_DD2199D2CC68C60 (implementation_project_id), INDEX IDX_DD2199D9E6B1585 (organisation_id), PRIMARY KEY(implementation_project_id, organisation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested ADD CONSTRAINT FK_86C0C7C42CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested ADD CONSTRAINT FK_86C0C7C49E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation ADD CONSTRAINT FK_DD2199D2CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation ADD CONSTRAINT FK_DD2199D9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('DROP TABLE ozg_implementation_project_contact_interested');
        $this->addSql('DROP TABLE ozg_implementation_project_contact_participation');
        $sql = 'REPLACE INTO ozg_service_jurisdiction (service_id, jurisdiction_id)
        SELECT s.id, ssj.jurisdiction_id FROM ozg_service s, ozg_service_system_jurisdiction ssj
       WHERE s.inherit_jurisdictions = 1 AND ssj.service_system_id = s.service_system_id';
        $this->addSql($sql);
        $sql = 'REPLACE INTO ozg_bureau_service (bureau_id, service_id)
        SELECT ssb.bureau_id, s.id FROM ozg_service s, ozg_bureau_service_system ssb
       WHERE s.inherit_bureaus = 1 AND ssb.service_system_id = s.service_system_id';
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_mailing_commune (mailing_id INT NOT NULL, commune_id INT NOT NULL, INDEX IDX_18D327273931AB76 (mailing_id), INDEX IDX_18D32727131A4F72 (commune_id), PRIMARY KEY(mailing_id, commune_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ozg_mailing_ministry_state (mailing_id INT NOT NULL, ministry_state_id INT NOT NULL, INDEX IDX_D90936923931AB76 (mailing_id), INDEX IDX_D9093692803929A9 (ministry_state_id), PRIMARY KEY(mailing_id, ministry_state_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ozg_mailing_service_provider (mailing_id INT NOT NULL, service_provider_id INT NOT NULL, INDEX IDX_FCCF14E63931AB76 (mailing_id), INDEX IDX_FCCF14E6C6C98E06 (service_provider_id), PRIMARY KEY(mailing_id, service_provider_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ozg_mailing_commune ADD CONSTRAINT FK_18D32727131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_commune ADD CONSTRAINT FK_18D327273931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_ministry_state ADD CONSTRAINT FK_D90936923931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_ministry_state ADD CONSTRAINT FK_D9093692803929A9 FOREIGN KEY (ministry_state_id) REFERENCES ozg_ministry_state (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_service_provider ADD CONSTRAINT FK_FCCF14E63931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_mailing_service_provider ADD CONSTRAINT FK_FCCF14E6C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE ozg_mailing_organisation');
        $this->addSql('ALTER TABLE ozg_contact ADD ministry_state_id INT DEFAULT NULL, ADD service_provider_id INT DEFAULT NULL, ADD commune_id INT DEFAULT NULL, ADD manufacturer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF202131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id)');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF202803929A9 FOREIGN KEY (ministry_state_id) REFERENCES ozg_ministry_state (id)');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF202A23B42D FOREIGN KEY (manufacturer_id) REFERENCES ozg_manufacturer (id)');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF202C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id)');
        $this->addSql('CREATE INDEX IDX_2CCAF202C6C98E06 ON ozg_contact (service_provider_id)');
        $this->addSql('CREATE INDEX IDX_2CCAF202A23B42D ON ozg_contact (manufacturer_id)');
        $this->addSql('CREATE INDEX IDX_2CCAF202803929A9 ON ozg_contact (ministry_state_id)');
        $this->addSql('CREATE INDEX IDX_2CCAF202131A4F72 ON ozg_contact (commune_id)');
        $this->addSql('ALTER TABLE ozg_organisation ADD ref_entity_id INT DEFAULT NULL');
        $this->addSql('CREATE TABLE ozg_implementation_project_contact_interested (implementation_project_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_57D4B5932CC68C60 (implementation_project_id), INDEX IDX_57D4B593E7A1254A (contact_id), PRIMARY KEY(implementation_project_id, contact_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ozg_implementation_project_contact_participation (implementation_project_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_63F2EE702CC68C60 (implementation_project_id), INDEX IDX_63F2EE70E7A1254A (contact_id), PRIMARY KEY(implementation_project_id, contact_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact_interested ADD CONSTRAINT FK_57D4B5932CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact_interested ADD CONSTRAINT FK_57D4B593E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact_participation ADD CONSTRAINT FK_63F2EE702CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact_participation ADD CONSTRAINT FK_63F2EE70E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id)');
        $this->addSql('DROP TABLE ozg_implementation_project_organisation_interested');
        $this->addSql('DROP TABLE ozg_implementation_project_organisation_participation');
    }
}
