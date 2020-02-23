<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200222200505 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_organisation (id INT AUTO_INCREMENT NOT NULL, ministry_state_id INT DEFAULT NULL, service_provider_id INT DEFAULT NULL, manufacturer_id INT DEFAULT NULL, commune_id INT DEFAULT NULL, organization_type VARCHAR(255) DEFAULT NULL, ref_entity_id INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_609D3524803929A9 (ministry_state_id), UNIQUE INDEX UNIQ_609D3524C6C98E06 (service_provider_id), UNIQUE INDEX UNIQ_609D3524A23B42D (manufacturer_id), UNIQUE INDEX UNIQ_609D3524131A4F72 (commune_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_organisation ADD CONSTRAINT FK_609D3524803929A9 FOREIGN KEY (ministry_state_id) REFERENCES ozg_ministry_state (id)');
        $this->addSql('ALTER TABLE ozg_organisation ADD CONSTRAINT FK_609D3524C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id)');
        $this->addSql('ALTER TABLE ozg_organisation ADD CONSTRAINT FK_609D3524A23B42D FOREIGN KEY (manufacturer_id) REFERENCES ozg_manufacturer (id)');
        $this->addSql('ALTER TABLE ozg_organisation ADD CONSTRAINT FK_609D3524131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id)');
        $this->addSql('ALTER TABLE ozg_commune ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_commune ADD CONSTRAINT FK_824AC5D49E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_824AC5D49E6B1585 ON ozg_commune (organisation_id)');
        $this->addSql('ALTER TABLE ozg_service_provider ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service_provider ADD CONSTRAINT FK_54ECCF6F9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_54ECCF6F9E6B1585 ON ozg_service_provider (organisation_id)');
        $this->addSql('ALTER TABLE ozg_ministry_state ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_ministry_state ADD CONSTRAINT FK_D9F8E8FE9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D9F8E8FE9E6B1585 ON ozg_ministry_state (organisation_id)');
        $this->addSql('ALTER TABLE ozg_contact ADD manufacturer_id INT DEFAULT NULL, ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF202A23B42D FOREIGN KEY (manufacturer_id) REFERENCES ozg_manufacturer (id)');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF2029E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('CREATE INDEX IDX_2CCAF202A23B42D ON ozg_contact (manufacturer_id)');
        $this->addSql('CREATE INDEX IDX_2CCAF2029E6B1585 ON ozg_contact (organisation_id)');
        $this->addSql('ALTER TABLE ozg_manufacturer ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_manufacturer ADD CONSTRAINT FK_BB76E14C9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB76E14C9E6B1585 ON ozg_manufacturer (organisation_id)');

        $tables = [
            'ozg_commune' => 'commune',
            'ozg_service_provider' => 'service_provider',
            'ozg_ministry_state' => 'ministry_state',
            'ozg_manufacturer' => 'manufacturer',
        ];
        $mapIdRefCols = [
            'ozg_commune' => 'commune_id',
            'ozg_service_provider' => 'service_provider_id',
            'ozg_ministry_state' => 'ministry_state_id',
            'ozg_manufacturer' => 'manufacturer_id',
        ];
        foreach ($tables as $table => $organisationType) {
            $sql = 'INSERT INTO ozg_organisation (organization_type, modified_at, created_at, name, hidden, street, zip_code, town, url, ref_entity_id)'
                . ' SELECT \''.$organisationType.'\', modified_at, created_at, name, hidden, street, zip_code, town, url, id'
                . ' FROM ' . $table .  ' ORDER BY id ASC';
            $this->addSql($sql);
            $refCol = $mapIdRefCols[$table];
            $sql = 'UPDATE ozg_organisation o, '.$table.' s SET s.organisation_id = o.id,  o.'.$refCol.' = s.id WHERE o.organization_type = \''.$organisationType.'\' AND o.ref_entity_id = s.id';
            $this->addSql($sql);
            $refCol = $mapIdRefCols[$table];
            $sql = 'UPDATE ozg_contact c, '.$table.' s SET c.organisation_id = s.organisation_id WHERE c.'.$refCol.' = s.id';
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_commune DROP FOREIGN KEY FK_824AC5D49E6B1585');
        $this->addSql('ALTER TABLE ozg_service_provider DROP FOREIGN KEY FK_54ECCF6F9E6B1585');
        $this->addSql('ALTER TABLE ozg_ministry_state DROP FOREIGN KEY FK_D9F8E8FE9E6B1585');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF2029E6B1585');
        $this->addSql('ALTER TABLE ozg_manufacturer DROP FOREIGN KEY FK_BB76E14C9E6B1585');
        $this->addSql('DROP TABLE ozg_organisation');
        $this->addSql('DROP INDEX UNIQ_824AC5D49E6B1585 ON ozg_commune');
        $this->addSql('ALTER TABLE ozg_commune DROP organisation_id');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF202A23B42D');
        $this->addSql('DROP INDEX IDX_2CCAF202A23B42D ON ozg_contact');
        $this->addSql('DROP INDEX IDX_2CCAF2029E6B1585 ON ozg_contact');
        $this->addSql('ALTER TABLE ozg_contact DROP manufacturer_id, DROP organisation_id');
        $this->addSql('DROP INDEX UNIQ_BB76E14C9E6B1585 ON ozg_manufacturer');
        $this->addSql('ALTER TABLE ozg_manufacturer DROP organisation_id');
        $this->addSql('DROP INDEX UNIQ_D9F8E8FE9E6B1585 ON ozg_ministry_state');
        $this->addSql('ALTER TABLE ozg_ministry_state DROP organisation_id');
        $this->addSql('DROP INDEX UNIQ_54ECCF6F9E6B1585 ON ozg_service_provider');
        $this->addSql('ALTER TABLE ozg_service_provider DROP organisation_id');
    }
}
