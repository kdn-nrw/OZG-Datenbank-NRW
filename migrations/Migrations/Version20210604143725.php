<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210604143725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add onboarding release table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_onboarding_release (id INT NOT NULL, release_status INT DEFAULT NULL, release_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', release_confirmed TINYINT(1) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(100) DEFAULT NULL, mobile_number VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_onboarding_release ADD CONSTRAINT FK_34982DCFBF396750 FOREIGN KEY (id) REFERENCES ozg_onboarding (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_onboarding_release');
    }
}
