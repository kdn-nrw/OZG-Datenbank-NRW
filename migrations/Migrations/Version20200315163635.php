<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200315163635 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_service_ministry_state (service_id INT NOT NULL, ministry_state_id INT NOT NULL, INDEX IDX_804892BAED5CA9E6 (service_id), INDEX IDX_804892BA803929A9 (ministry_state_id), PRIMARY KEY(service_id, ministry_state_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_service_rule_authority (service_id INT NOT NULL, jurisdiction_id INT NOT NULL, INDEX IDX_23C09A5DED5CA9E6 (service_id), INDEX IDX_23C09A5D8C52AF17 (jurisdiction_id), PRIMARY KEY(service_id, jurisdiction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_service_authority_ministry_state (service_id INT NOT NULL, ministry_state_id INT NOT NULL, INDEX IDX_D086E901ED5CA9E6 (service_id), INDEX IDX_D086E901803929A9 (ministry_state_id), PRIMARY KEY(service_id, ministry_state_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_service_authority_bureau (service_id INT NOT NULL, bureau_id INT NOT NULL, INDEX IDX_4FFB63FEED5CA9E6 (service_id), INDEX IDX_4FFB63FE32516FE2 (bureau_id), PRIMARY KEY(service_id, bureau_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_service_system_rule_authority (service_system_id INT NOT NULL, jurisdiction_id INT NOT NULL, INDEX IDX_8A008D14880415EF (service_system_id), INDEX IDX_8A008D148C52AF17 (jurisdiction_id), PRIMARY KEY(service_system_id, jurisdiction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_service_system_authority_ministry_state (service_system_id INT NOT NULL, ministry_state_id INT NOT NULL, INDEX IDX_D21D3B59880415EF (service_system_id), INDEX IDX_D21D3B59803929A9 (ministry_state_id), PRIMARY KEY(service_system_id, ministry_state_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_service_system_authority_bureau (service_system_id INT NOT NULL, bureau_id INT NOT NULL, INDEX IDX_ED93BBB5880415EF (service_system_id), INDEX IDX_ED93BBB532516FE2 (bureau_id), PRIMARY KEY(service_system_id, bureau_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_service_ministry_state ADD CONSTRAINT FK_804892BAED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
        $this->addSql('ALTER TABLE ozg_service_ministry_state ADD CONSTRAINT FK_804892BA803929A9 FOREIGN KEY (ministry_state_id) REFERENCES ozg_ministry_state (id)');
        $this->addSql('ALTER TABLE ozg_service_rule_authority ADD CONSTRAINT FK_23C09A5DED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
        $this->addSql('ALTER TABLE ozg_service_rule_authority ADD CONSTRAINT FK_23C09A5D8C52AF17 FOREIGN KEY (jurisdiction_id) REFERENCES ozg_jurisdiction (id)');
        $this->addSql('ALTER TABLE ozg_service_authority_ministry_state ADD CONSTRAINT FK_D086E901ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
        $this->addSql('ALTER TABLE ozg_service_authority_ministry_state ADD CONSTRAINT FK_D086E901803929A9 FOREIGN KEY (ministry_state_id) REFERENCES ozg_ministry_state (id)');
        $this->addSql('ALTER TABLE ozg_service_authority_bureau ADD CONSTRAINT FK_4FFB63FEED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
        $this->addSql('ALTER TABLE ozg_service_authority_bureau ADD CONSTRAINT FK_4FFB63FE32516FE2 FOREIGN KEY (bureau_id) REFERENCES ozg_bureau (id)');
        $this->addSql('ALTER TABLE ozg_service_system_rule_authority ADD CONSTRAINT FK_8A008D14880415EF FOREIGN KEY (service_system_id) REFERENCES ozg_service_system (id)');
        $this->addSql('ALTER TABLE ozg_service_system_rule_authority ADD CONSTRAINT FK_8A008D148C52AF17 FOREIGN KEY (jurisdiction_id) REFERENCES ozg_jurisdiction (id)');
        $this->addSql('ALTER TABLE ozg_service_system_authority_ministry_state ADD CONSTRAINT FK_D21D3B59880415EF FOREIGN KEY (service_system_id) REFERENCES ozg_service_system (id)');
        $this->addSql('ALTER TABLE ozg_service_system_authority_ministry_state ADD CONSTRAINT FK_D21D3B59803929A9 FOREIGN KEY (ministry_state_id) REFERENCES ozg_ministry_state (id)');
        $this->addSql('ALTER TABLE ozg_service_system_authority_bureau ADD CONSTRAINT FK_ED93BBB5880415EF FOREIGN KEY (service_system_id) REFERENCES ozg_service_system (id)');
        $this->addSql('ALTER TABLE ozg_service_system_authority_bureau ADD CONSTRAINT FK_ED93BBB532516FE2 FOREIGN KEY (bureau_id) REFERENCES ozg_bureau (id)');
        $this->addSql('ALTER TABLE ozg_service ADD inherit_state_ministries TINYINT(1) NOT NULL, ADD inherit_rule_authorities TINYINT(1) NOT NULL, ADD authority_inherit_state_ministries TINYINT(1) NOT NULL, ADD authority_inherit_bureaus TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE ozg_service_system ADD created_by INT DEFAULT NULL, ADD modified_by INT DEFAULT NULL, CHANGE name name VARCHAR(1024) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service_system ADD CONSTRAINT FK_18AF8D78DE12AB56 FOREIGN KEY (created_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_service_system ADD CONSTRAINT FK_18AF8D7825F94802 FOREIGN KEY (modified_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_18AF8D78DE12AB56 ON ozg_service_system (created_by)');
        $this->addSql('CREATE INDEX IDX_18AF8D7825F94802 ON ozg_service_system (modified_by)');
        $this->addSql('UPDATE ozg_service SET inherit_rule_authorities = 1');
        $this->addSql('UPDATE ozg_service SET inherit_state_ministries = 1');
        $this->addSql('UPDATE ozg_service SET authority_inherit_state_ministries = 1');
        $this->addSql('UPDATE ozg_service SET authority_inherit_bureaus = 1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_service_ministry_state');
        $this->addSql('DROP TABLE ozg_service_rule_authority');
        $this->addSql('DROP TABLE ozg_service_authority_ministry_state');
        $this->addSql('DROP TABLE ozg_service_authority_bureau');
        $this->addSql('DROP TABLE ozg_service_system_rule_authority');
        $this->addSql('DROP TABLE ozg_service_system_authority_ministry_state');
        $this->addSql('DROP TABLE ozg_service_system_authority_bureau');
        $this->addSql('ALTER TABLE ozg_service ADD description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD service_key VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP inherit_state_ministries, DROP inherit_rule_authorities, DROP authority_inherit_state_ministries, DROP authority_inherit_bureaus');
        $this->addSql('ALTER TABLE ozg_service_system DROP FOREIGN KEY FK_18AF8D78DE12AB56');
        $this->addSql('ALTER TABLE ozg_service_system DROP FOREIGN KEY FK_18AF8D7825F94802');
        $this->addSql('DROP INDEX IDX_18AF8D78DE12AB56 ON ozg_service_system');
        $this->addSql('DROP INDEX IDX_18AF8D7825F94802 ON ozg_service_system');
        $this->addSql('ALTER TABLE ozg_service_system ADD service_key VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP created_by, DROP modified_by, CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
