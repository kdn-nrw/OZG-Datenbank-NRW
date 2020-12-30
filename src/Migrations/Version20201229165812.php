<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229165812 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_service_provider_security_incident (id INT AUTO_INCREMENT NOT NULL, service_provider_id INT DEFAULT NULL, occurred_on DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', solved_on DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', subject_type INT NOT NULL, description LONGTEXT DEFAULT NULL, affected VARCHAR(255) DEFAULT NULL, extent INT NOT NULL, method INT NOT NULL, cause LONGTEXT DEFAULT NULL, measures LONGTEXT DEFAULT NULL, informed_parties LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_644A5333C6C98E06 (service_provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_service_provider_security_incident ADD CONSTRAINT FK_644A5333C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id)');
        $this->addSql('ALTER TABLE ozg_service_provider_security_incident CHANGE subject_type subject_type INT DEFAULT NULL, CHANGE extent extent INT DEFAULT NULL, CHANGE method method INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service_provider_security_incident ADD created_by INT DEFAULT NULL, ADD modified_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service_provider_security_incident ADD CONSTRAINT FK_644A5333DE12AB56 FOREIGN KEY (created_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_service_provider_security_incident ADD CONSTRAINT FK_644A533325F94802 FOREIGN KEY (modified_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_644A5333DE12AB56 ON ozg_service_provider_security_incident (created_by)');
        $this->addSql('CREATE INDEX IDX_644A533325F94802 ON ozg_service_provider_security_incident (modified_by)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_service_provider_security_incident');
    }
}
