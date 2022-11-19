<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210627160455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('UPDATE ozg_onboarding_epayment_service SET service_id = NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment_service DROP FOREIGN KEY FK_B3A5D4E4ED5CA9E6');
        $this->addSql('DROP TABLE ozg_onboarding_service');
        $this->addSql('DROP INDEX IDX_B3A5D4E4ED5CA9E6 ON ozg_onboarding_epayment_service');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment_service CHANGE service_id solution_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment_service ADD CONSTRAINT FK_B3A5D4E41C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('CREATE INDEX IDX_B3A5D4E41C0BE183 ON ozg_onboarding_epayment_service (solution_id)');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_onboarding_service (id INT AUTO_INCREMENT NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, hidden TINYINT(1) NOT NULL, url VARCHAR(2048) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment_service DROP FOREIGN KEY FK_B3A5D4E41C0BE183');
        $this->addSql('DROP INDEX IDX_B3A5D4E41C0BE183 ON ozg_onboarding_epayment_service');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment_service CHANGE solution_id service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment_service ADD CONSTRAINT FK_B3A5D4E4ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_onboarding_service (id)');
        $this->addSql('CREATE INDEX IDX_B3A5D4E4ED5CA9E6 ON ozg_onboarding_epayment_service (service_id)');
    }
}
