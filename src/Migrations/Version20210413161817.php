<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413161817 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add service payment provider flag; assign payment providers to administrative districts';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_administrative_district ADD payment_operator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_administrative_district ADD CONSTRAINT FK_206B7544AEC94879 FOREIGN KEY (payment_operator_id) REFERENCES ozg_service_provider (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_206B7544AEC94879 ON ozg_administrative_district (payment_operator_id)');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment ADD payment_operator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment ADD CONSTRAINT FK_651A4613AEC94879 FOREIGN KEY (payment_operator_id) REFERENCES ozg_service_provider (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_651A4613AEC94879 ON ozg_onboarding_epayment (payment_operator_id)');
        $this->addSql('ALTER TABLE ozg_service_provider ADD enable_payment_provider TINYINT(1) DEFAULT NULL');
        $this->addSql('UPDATE ozg_service_provider SET enable_payment_provider = 1 WHERE id IN (10, 26)');
        $this->addSql('UPDATE ozg_administrative_district SET payment_operator_id = 10 WHERE id IN (2, 4, 5)');
        $this->addSql('UPDATE ozg_administrative_district SET payment_operator_id = 26 WHERE id IN (1, 3)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_administrative_district DROP FOREIGN KEY FK_206B7544AEC94879');
        $this->addSql('DROP INDEX IDX_206B7544AEC94879 ON ozg_administrative_district');
        $this->addSql('ALTER TABLE ozg_administrative_district DROP payment_operator_id');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment DROP FOREIGN KEY FK_651A4613AEC94879');
        $this->addSql('DROP INDEX IDX_651A4613AEC94879 ON ozg_onboarding_epayment');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment DROP payment_operator_id');
        $this->addSql('ALTER TABLE ozg_service_provider DROP enable_payment_provider');
    }
}
