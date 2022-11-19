<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201016142507 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE IF NOT EXISTS ozg_commune_type (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, constituency TINYINT(1) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $insertCommuneTypes = [
            1 => 'Städteregion',
            2 => 'Kreis',
            3 => 'kreisangehörige Gemeinde',
            4 => 'Große kreisangehörige Stadt',
            5 => 'kreisfreie Stadt',
            6 => 'Mittlere kreisangehörige Stadt',
            7 => 'kreisangehörige Stadt',
        ];
        foreach ($insertCommuneTypes as $id => $name) {
            $constituency = in_array($id, [1,2], false) ? 1 : 0;
            $this->addSql('INSERT INTO ozg_commune_type (id, name, created_at, hidden, constituency) VALUES('.$id.', \''.$name.'\', NOW(), 0, '. $constituency . ')');
        }
        $this->addSql('CREATE TABLE ozg_service_commune_type (service_id INT NOT NULL, commune_type_id INT NOT NULL, INDEX IDX_691462C2ED5CA9E6 (service_id), INDEX IDX_691462C223727C7A (commune_type_id), PRIMARY KEY(service_id, commune_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_service_system_commune_type (service_system_id INT NOT NULL, commune_type_id INT NOT NULL, INDEX IDX_CE1390C880415EF (service_system_id), INDEX IDX_CE1390C23727C7A (commune_type_id), PRIMARY KEY(service_system_id, commune_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_service_commune_type ADD CONSTRAINT FK_691462C2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
        $this->addSql('ALTER TABLE ozg_service_commune_type ADD CONSTRAINT FK_691462C223727C7A FOREIGN KEY (commune_type_id) REFERENCES ozg_commune_type (id)');
        $this->addSql('ALTER TABLE ozg_service_system_commune_type ADD CONSTRAINT FK_CE1390C880415EF FOREIGN KEY (service_system_id) REFERENCES ozg_service_system (id)');
        $this->addSql('ALTER TABLE ozg_service_system_commune_type ADD CONSTRAINT FK_CE1390C23727C7A FOREIGN KEY (commune_type_id) REFERENCES ozg_commune_type (id)');
        $tableDefinition = $schema->getTable('ozg_commune');
        if ($tableDefinition->hasColumn('commune_type')) {
            $this->addSql('ALTER TABLE ozg_commune CHANGE commune_type commune_type_id INT DEFAULT NULL');
        } else {
            $this->addSql('ALTER TABLE ozg_commune ADD commune_type_id INT DEFAULT NULL');
        }
        $this->addSql('ALTER TABLE ozg_commune ADD CONSTRAINT FK_824AC5D423727C7A FOREIGN KEY (commune_type_id) REFERENCES ozg_commune_type (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_824AC5D423727C7A ON ozg_commune (commune_type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_commune DROP FOREIGN KEY FK_824AC5D423727C7A');
        $this->addSql('ALTER TABLE ozg_service_commune_type DROP FOREIGN KEY FK_691462C223727C7A');
        $this->addSql('ALTER TABLE ozg_service_system_commune_type DROP FOREIGN KEY FK_CE1390C23727C7A');
        $this->addSql('DROP TABLE ozg_commune_type');
        $this->addSql('DROP TABLE ozg_service_commune_type');
        $this->addSql('DROP TABLE ozg_service_system_commune_type');
        $this->addSql('DROP INDEX IDX_824AC5D423727C7A ON ozg_commune');
        $this->addSql('ALTER TABLE ozg_commune CHANGE commune_type_id commune_type INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service ADD commune_types LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE ozg_service_system ADD commune_types LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\'');
    }
}
