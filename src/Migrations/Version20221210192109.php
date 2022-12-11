<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221210192109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add pmPayment tables and fields';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_onboarding_pm_payment (id INT NOT NULL, endpoint_system_test VARCHAR(255) DEFAULT NULL, password_system_test VARCHAR(255) DEFAULT NULL, endpoint_system_production VARCHAR(255) DEFAULT NULL, password_system_production VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(100) DEFAULT NULL, mobile_number VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding_pm_payment_service (id INT AUTO_INCREMENT NOT NULL, solution_id INT DEFAULT NULL, pm_payment_id INT DEFAULT NULL, payment_method_name LONGTEXT DEFAULT NULL, payment_method_prefix LONGTEXT DEFAULT NULL, payment_method_start_nr INT DEFAULT NULL, payment_method_end_nr INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) NOT NULL, position INT DEFAULT NULL, INDEX IDX_5008802D1C0BE183 (solution_id), INDEX IDX_5008802DF72B5CF (pm_payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_onboarding_pm_payment ADD CONSTRAINT FK_57CB1699BF396750 FOREIGN KEY (id) REFERENCES ozg_onboarding (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding_pm_payment_service ADD CONSTRAINT FK_5008802D1C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('ALTER TABLE ozg_onboarding_pm_payment_service ADD CONSTRAINT FK_5008802DF72B5CF FOREIGN KEY (pm_payment_id) REFERENCES ozg_onboarding_pm_payment (id)');
        $this->addSql('ALTER TABLE ozg_onboarding_commune_solution ADD enabled_pm_payment TINYINT(1) NOT NULL');
        $this->addSql('CREATE TABLE ozg_onboarding_pm_payment_audit (id INT NOT NULL, rev INT NOT NULL, endpoint_system_test VARCHAR(255) DEFAULT NULL, password_system_test VARCHAR(255) DEFAULT NULL, endpoint_system_production VARCHAR(255) DEFAULT NULL, password_system_production VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(100) DEFAULT NULL, mobile_number VARCHAR(100) DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_b033066fbb79ed5264ea52b5bc05a0d1_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_onboarding_pm_payment_service_audit (id INT NOT NULL, rev INT NOT NULL, solution_id INT DEFAULT NULL, pm_payment_id INT DEFAULT NULL, payment_method_name LONGTEXT DEFAULT NULL, payment_method_prefix LONGTEXT DEFAULT NULL, payment_method_start_nr INT DEFAULT NULL, payment_method_end_nr INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) DEFAULT NULL, position INT DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_312fde03fa60f003767a01a373126c97_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_onboarding_pm_payment_audit ADD CONSTRAINT rev_b033066fbb79ed5264ea52b5bc05a0d1_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE ozg_onboarding_pm_payment_service_audit ADD CONSTRAINT rev_312fde03fa60f003767a01a373126c97_fk FOREIGN KEY (rev) REFERENCES revisions (id)');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_pm_payment_service DROP FOREIGN KEY FK_5008802DF72B5CF');
        $this->addSql('DROP TABLE ozg_onboarding_pm_payment');
        $this->addSql('DROP TABLE ozg_onboarding_pm_payment_service');
        $this->addSql('ALTER TABLE ozg_onboarding_commune_solution DROP enabled_pm_payment');
        $this->addSql('DROP TABLE ozg_onboarding_pm_payment_audit');
        $this->addSql('DROP TABLE ozg_onboarding_pm_payment_service_audit');
    }
}
