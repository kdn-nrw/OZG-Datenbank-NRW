<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\StateGroup\Commune;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201010084648 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_administrative_district (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_commune ADD constituency_id INT DEFAULT NULL, ADD administrative_district_id INT DEFAULT NULL, ADD commune_type INT DEFAULT NULL, ADD main_email VARCHAR(255) DEFAULT NULL, ADD official_community_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_commune ADD CONSTRAINT FK_824AC5D4693B626F FOREIGN KEY (constituency_id) REFERENCES ozg_commune (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_commune ADD CONSTRAINT FK_824AC5D4BF5C3310 FOREIGN KEY (administrative_district_id) REFERENCES ozg_administrative_district (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_824AC5D4BF5C3310 ON ozg_commune (administrative_district_id)');
        $this->addSql('UPDATE ozg_commune SET commune_type = ' . Commune::TYPE_CONSTITUENCY . ' WHERE name LIKE \'%Kreis%\'');
        $this->addSql('UPDATE ozg_commune SET commune_type = ' . Commune::TYPE_CITY_REGION . ' WHERE name LIKE \'%Städteregion%\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_commune DROP FOREIGN KEY FK_824AC5D4BF5C3310');
        $this->addSql('ALTER TABLE ozg_commune DROP FOREIGN KEY FK_824AC5D4693B626F');
        $this->addSql('DROP TABLE ozg_administrative_district');
        $this->addSql('DROP INDEX IDX_824AC5D4693B626F ON ozg_commune');
        $this->addSql('DROP INDEX IDX_824AC5D4BF5C3310 ON ozg_commune');
        $this->addSql('ALTER TABLE ozg_commune DROP constituency_id, DROP administrative_district_id, DROP commune_type, DROP main_email, DROP official_community_key');
    }
}
