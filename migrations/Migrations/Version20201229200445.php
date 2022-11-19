<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229200445 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_user_service_provider_mm (user_id INT NOT NULL, service_provider_id INT NOT NULL, INDEX IDX_4D641798A76ED395 (user_id), INDEX IDX_4D641798C6C98E06 (service_provider_id), PRIMARY KEY(user_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_user_service_provider_mm ADD CONSTRAINT FK_4D641798A76ED395 FOREIGN KEY (user_id) REFERENCES mb_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_user_service_provider_mm ADD CONSTRAINT FK_4D641798C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mb_user_user ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mb_user_user ADD CONSTRAINT FK_9C94D79B9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_9C94D79B9E6B1585 ON mb_user_user (organisation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_user_service_provider_mm');
        $this->addSql('ALTER TABLE mb_user_user DROP FOREIGN KEY FK_9C94D79B9E6B1585');
        $this->addSql('DROP INDEX IDX_9C94D79B9E6B1585 ON mb_user_user');
        $this->addSql('ALTER TABLE mb_user_user DROP organisation_id');
    }
}
