<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220815141656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add monument authority table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_onboarding_monument_authority (id INT NOT NULL, application_type INT DEFAULT NULL, intermediary_operator_type INT DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, authority_category VARCHAR(100) DEFAULT NULL, organizational_key VARCHAR(100) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, osci_private_key_password VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding_monument_authority_audit (id INT NOT NULL, rev INT NOT NULL, application_type INT DEFAULT NULL, intermediary_operator_type INT DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, authority_category VARCHAR(100) DEFAULT NULL, organizational_key VARCHAR(100) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, osci_private_key_password VARCHAR(255) DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_433cb4022bcbbb19dc30b94d3d3453d0_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_onboarding_monument_authority ADD CONSTRAINT FK_5F1C0F5CBF396750 FOREIGN KEY (id) REFERENCES ozg_onboarding (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding_contact ADD monument_authority_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_contact ADD CONSTRAINT FK_E6BDC8EAA10ABDFD FOREIGN KEY (monument_authority_id) REFERENCES ozg_onboarding_monument_authority (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6BDC8EAA10ABDFD ON ozg_onboarding_contact (monument_authority_id)');
        $this->addSql('ALTER TABLE ozg_onboarding_contact_audit ADD monument_authority_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP FOREIGN KEY FK_E6BDC8EAA10ABDFD');
        $this->addSql('DROP TABLE ozg_onboarding_monument_authority');
        $this->addSql('DROP TABLE ozg_onboarding_monument_authority_audit');
        $this->addSql('DROP INDEX UNIQ_E6BDC8EAA10ABDFD ON ozg_onboarding_contact');
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP monument_authority_id');
        $this->addSql('ALTER TABLE ozg_onboarding_contact_audit DROP monument_authority_id');
    }
}
