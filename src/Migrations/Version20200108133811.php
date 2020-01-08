<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200108133811 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_bureau (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_bureau_service_system (bureau_id INT NOT NULL, service_system_id INT NOT NULL, INDEX IDX_A3C797A232516FE2 (bureau_id), INDEX IDX_A3C797A2880415EF (service_system_id), PRIMARY KEY(bureau_id, service_system_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_bureau_service_system ADD CONSTRAINT FK_A3C797A232516FE2 FOREIGN KEY (bureau_id) REFERENCES ozg_bureau (id)');
        $this->addSql('ALTER TABLE ozg_bureau_service_system ADD CONSTRAINT FK_A3C797A2880415EF FOREIGN KEY (service_system_id) REFERENCES ozg_service_system (id)');
        $this->addSql('INSERT INTO ozg_bureau (name, hidden) VALUES (\'Hauptamt\', 0), (\'Personalamt\', 0), (\'Statistisches Amt\', 0), (\'Presseamt\', 0), (\'Rechnungsprüfungsamt\', 0), (\'Kämmerei\', 0), (\'Stadtkasse\', 0), (\'Steueramt\', 0), (\'Liegenschaftsamt\', 0), (\'Amt für Verteidigungslasten\', 0), (\'Rechtsamt\', 0), (\'Ordnungsamt\', 0), (\'Einwohner- und Meldeamt\', 0), (\'Standesamt\', 0), (\'Versicherungsamt\', 0), (\'Feuerwehr\', 0), (\'Zivilschutz\', 0), (\'Schulverwaltungsamt\', 0), (\'Kulturamt\', 0), (\'Bibliothek\', 0), (\'Volkshochschule\', 0), (\'Musikschule\', 0), (\'Museum\', 0), (\'Theater\', 0), (\'Archiv\', 0), (\'Sozialamt\', 0), (\'Jugendamt\', 0), (\'Sportamt\', 0), (\'Gesundheitsamt\', 0), (\'Krankenhäuser\', 0), (\'Ausgleichsamt\', 0), (\'Bauverwaltungsamt\', 0), (\'Stadtplanungsamt\', 0), (\'Vermessungs- und Katasteramt\', 0), (\'Bauordnungsamt\', 0), (\'Wohnungsförderungsamt\', 0), (\'Hochbauamt\', 0), (\'Tiefbauamt\', 0), (\'Grünflächenamt\', 0), (\'Stadtreinigungamt\', 0), (\'Schlacht- und Viehhof\', 0), (\'Marktamt\', 0), (\'Amt für Wirtschafts- und Verkehrsförderung\', 0), (\'Eigenbetriebe\', 0), (\'Forstamt\', 0)');
        $this->addSql('UPDATE ozg_bureau SET created_at = NOW(), modified_at = NOW()');
        $this->addSql('ALTER TABLE ozg_category DROP FOREIGN KEY FK_C0206867727ACA70');
        $this->addSql('ALTER TABLE ozg_category ADD CONSTRAINT FK_C0206867727ACA70 FOREIGN KEY (parent_id) REFERENCES ozg_category (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_bureau_service_system DROP FOREIGN KEY FK_A3C797A232516FE2');
        $this->addSql('DROP TABLE ozg_bureau');
        $this->addSql('DROP TABLE ozg_bureau_service_system');
        $this->addSql('ALTER TABLE ozg_category DROP FOREIGN KEY FK_C0206867727ACA70');
        $this->addSql('ALTER TABLE ozg_category ADD CONSTRAINT FK_C0206867727ACA70 FOREIGN KEY (parent_id) REFERENCES ozg_category (id)');
    }
}
