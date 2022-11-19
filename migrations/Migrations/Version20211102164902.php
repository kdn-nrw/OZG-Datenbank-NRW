<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211102164902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update application properties';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_application_business_premises (application_id INT NOT NULL, organisation_id INT NOT NULL, INDEX IDX_21854A6C3E030ACD (application_id), INDEX IDX_21854A6C9E6B1585 (organisation_id), PRIMARY KEY(application_id, organisation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_application_accessibility_test_organisations (application_id INT NOT NULL, organisation_id INT NOT NULL, INDEX IDX_1EEFD72B3E030ACD (application_id), INDEX IDX_1EEFD72B9E6B1585 (organisation_id), PRIMARY KEY(application_id, organisation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_application_accessibility_document (id INT AUTO_INCREMENT NOT NULL, application_id INT DEFAULT NULL, file_size INT DEFAULT NULL, local_name VARCHAR(255) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_D47F606A3E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_application_business_premises ADD CONSTRAINT FK_21854A6C3E030ACD FOREIGN KEY (application_id) REFERENCES ozg_application (id)');
        $this->addSql('ALTER TABLE ozg_application_business_premises ADD CONSTRAINT FK_21854A6C9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('ALTER TABLE ozg_application_accessibility_test_organisations ADD CONSTRAINT FK_1EEFD72B3E030ACD FOREIGN KEY (application_id) REFERENCES ozg_application (id)');
        $this->addSql('ALTER TABLE ozg_application_accessibility_test_organisations ADD CONSTRAINT FK_1EEFD72B9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('ALTER TABLE ozg_application_accessibility_document ADD CONSTRAINT FK_D47F606A3E030ACD FOREIGN KEY (application_id) REFERENCES ozg_application (id)');
        $this->addSql('ALTER TABLE ozg_application ADD accessibility_test_organisation_others LONGTEXT DEFAULT NULL, ADD accessibility_self_testing TINYINT(1) DEFAULT NULL, ADD accessibility_test_result_type INT NOT NULL, CHANGE accessibility accessibility_test_conducted LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_application_business_premises');
        $this->addSql('DROP TABLE ozg_application_accessibility_test_organisations');
        $this->addSql('DROP TABLE ozg_application_accessibility_document');
        $this->addSql('ALTER TABLE ozg_application ADD accessibility LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP accessibility_test_conducted, DROP accessibility_test_organisation_others, DROP accessibility_self_testing, DROP accessibility_test_result_type');
    }
}
