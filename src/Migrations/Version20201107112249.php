<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201107112249 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_api_consumer (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, proxy VARCHAR(2048) DEFAULT NULL, consumer_key VARCHAR(255) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, url VARCHAR(2048) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO symfony.ozg_api_consumer (id, description, proxy, consumer_key, modified_at, created_at, name, hidden, url) VALUES (1, \'Mithilfe der ARS/AGS-API v1.0 können amtlicher Gemeindeschlüssel (AGS) und amtlicher Regionalschlüssel (ARS) von Orten oder Gebieten ermittelt werden.\', null, \'ars_ags\', NOW(), NOW(), \'ARS/AGS-API v1.0\', 0, \'https://ags-ars.api.vsm.nrw/orte\')');
        $this->addSql('INSERT INTO symfony.ozg_api_consumer (id, description, proxy, consumer_key, modified_at, created_at, name, hidden, url) VALUES (2, \'Die LeiKa API v1.0 liefert zu einem eingegebenen Leistungsbegriff relevante Informationen zurück.\', null, \'leika\', NOW(), NOW(), \'LeiKa-API v1.0\', 0, \'https://leika.vsm.nrw/services\')');
        $this->addSql('INSERT INTO symfony.ozg_api_consumer (id, description, proxy, consumer_key, modified_at, created_at, name, hidden, url) VALUES (3, \'Die Web-Such-API liefert zu einem Suchbegriff Volltext-Treffer von den Websites, die vom Crawler der VSM durchsucht worden sind. Sie liefert keine Zuständigkeitsinformationen.\', null, \'web_search\', NOW(), NOW(), \'Web-Such-API v1.0\', 0, \'https://web-suche.api.vsm.nrw/web-treffer\')');
        $this->addSql('INSERT INTO symfony.ozg_api_consumer (id, description, proxy, consumer_key, modified_at, created_at, name, hidden, url) VALUES (4, \'Die ZuFi-API v1.0.2 liefert zu einem Regionalschlüssel oder einer Postleitzahl und zu einem Leistungsschlüssel Zuständigkeiten zurück.\', null, \'zu_fi\', NOW(), NOW(), \'ZuFi-API v1.0.2\', 0, \'https://zufi.api.vsm.nrw/zustaendigkeiten\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_api_consumer');
    }
}
