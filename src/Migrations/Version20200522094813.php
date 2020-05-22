<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200522094813 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_implementation_project_organisation_leaders (implementation_project_id INT NOT NULL, organisation_id INT NOT NULL, INDEX IDX_3C2291932CC68C60 (implementation_project_id), INDEX IDX_3C2291939E6B1585 (organisation_id), PRIMARY KEY(implementation_project_id, organisation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_implementation_project_funding (implementation_project_id INT NOT NULL, funding_id INT NOT NULL, INDEX IDX_23C5C0172CC68C60 (implementation_project_id), INDEX IDX_23C5C0179D70482 (funding_id), PRIMARY KEY(implementation_project_id, funding_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_funding (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_page_content (id INT AUTO_INCREMENT NOT NULL, page INT NOT NULL, position INT DEFAULT NULL, headline VARCHAR(255) DEFAULT NULL, bodytext LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_leaders ADD CONSTRAINT FK_3C2291932CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_leaders ADD CONSTRAINT FK_3C2291939E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_funding ADD CONSTRAINT FK_23C5C0172CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_funding ADD CONSTRAINT FK_23C5C0179D70482 FOREIGN KEY (funding_id) REFERENCES ozg_funding (id)');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions ADD position INT DEFAULT NULL');
        $this->addSql('INSERT INTO ozg_page_content (id, page, position, headline, bodytext, modified_at, created_at) VALUES (1, 1, 0, \'Verwaltungsdigitalisierung und OZG-Umsetzung der Kommunen in NRW\', \'<p>Diese offene Datenbank enth&auml;lt detaillierte Zahlen und Fakten zur Digitalisierung der Kommunalverwaltungen in Nordrhein-Westfalen.</p>

<p>Die Kommunen in NRW und ihre IT-Dienstleister im KDN pr&auml;sentieren hier ihre gemeinsamen Digitalisierungsprojekte und L&ouml;sungen zur Umsetzung des Onlinezugangsgesetzes (OZG):</p>

<ul>
	<li>Zust&auml;ndigkeiten und Beteiligte</li>
	<li>Priorisierungen</li>
	<li>Status-Informationen, zeitliche Planungen, Skizzen und weitere Details</li>
</ul>\', \'2020-05-22 10:10:20\', \'2020-05-22 10:10:20\');');
        $this->addSql('INSERT INTO ozg_page_content (id, page, position, headline, bodytext, modified_at, created_at) VALUES (2, 2, 0, \'OZG-Leistungen der Kommunen in NRW\', \'<p>Im Zuge der Umsetzung des Onlinezugangsgesetzes (OZG) hat der IT-Planungsrat den sogenannten OZG-Katalog beschlossen. Der OZG-Katalog b&uuml;ndelt die relevanten Eintr&auml;ge aus dem Leistungskatalog der deutschen Verwaltung (LeiKa) in OZG-Leistungen und fasst diese wiederum zusammen in OZG-Lebens- und Gesch&auml;ftslagen, die dann OZG-Themenfeldern zugeordnet sind. Die hier vorliegende Liste der OZG-Leistungen informiert:</p>

<ul>
	<li>F&uuml;r welche OZG-Leistungen sind die Kommunen in NRW zust&auml;ndig?</li>
	<li>Wie sind die Leistungen im OZG-Gesamtumsetzungsplan der Kommunen in NRW priorisiert?</li>
	<li>Welche L&ouml;sungen liegen zu den kommunal relevanten Leistungen schon vor?</li>
</ul>\', \'2020-05-22 10:10:41\', \'2020-05-22 10:10:41\');');
        $this->addSql('INSERT INTO ozg_page_content (id, page, position, headline, bodytext, modified_at, created_at) VALUES (3, 3, 0, \'Kommunale Verwaltungsleistungen in NRW\', \'<p>Die Zahl der Leistungen, die Kommunen in NRW f&uuml;r Menschen und Organisationen bereithalten, ist immens. Eine Methode der Klassifizierung von kommunalen Verwaltungsprozessen ist der Leistungskatalog der deutschen Verwaltung, genannt LeiKa. Hier entsteht f&uuml;r die Kommunen in NRW ein &Uuml;berblick:</p>

<ul>
	<li>Welche Leistungen sind in NRW kommunal? Welche Fach&auml;mter sind in der Regel zust&auml;ndig?</li>
	<li>Wie sind die Leistungen im OZG-Gesamtumsetzungsplan der Kommunen in NRW priorisiert?</li>
	<li>F&uuml;r welche kommunalen Leistungen gilt die europ&auml;ische Verordnung zum Single Digital Gateway (SDG)?</li>
</ul>

<p>Die &ouml;ffentliche Verwaltung entwickelt sich weiter. Die Bundes- und Landesredaktionen aktualisieren regelm&auml;&szlig;ig die Leika-Liste der deutschen Verwaltungsleistungen.</p>\', \'2020-05-22 10:11:08\', \'2020-05-22 10:11:08\');');
        $this->addSql('INSERT INTO ozg_page_content (id, page, position, headline, bodytext, modified_at, created_at) VALUES (4, 4, 0, \'Gemeinsame Digitalisierungsprojekte der Kommunen in NRW zur Umsetzung des OZG\', \'<p>Die Digitalisierung der &ouml;ffentlichen Verwaltung ist eine gro&szlig;e Aufgabe. Die Kommunen in NRW arbeiten zusammen mit dem Land, eingebettet in bundesweite und sogar europaweite Programme. Hier ist tagesaktuell die gemeinsame kommunale OZG-Umsetzung der Kommunen in NRW dokumentiert:</p>

<ul>
	<li>Welche gemeinsamen Umsetzungsprojekte bestehen? Wer ist daran aktiv beteiligt?</li>
	<li>Was ist der Stand der Umsetzung?</li>
	<li>Welche L&ouml;sung ben&ouml;tigt welche Basiskomponente in der Gesamtarchitektur (z. B. Servicekonto.NRW mit Authentifizierungsstufe, ePayBL zur Bezahlung etc.)?</li>
</ul>

<p>Die hier pr&auml;sentierten, gemeinsam und arbeitsteilig entwickelten L&ouml;sungen werden auf zentralen Infrastrukturen bereitgestellt. Das schafft Synergien und f&ouml;rdert die Standardisierung!</p>\', \'2020-05-22 10:11:30\', \'2020-05-22 10:11:30\');');
        $this->addSql('INSERT INTO ozg_page_content (id, page, position, headline, bodytext, modified_at, created_at) VALUES (5, 5, 0, \'IT-Lösungen und Online-Dienste der Kommunen in Nordrhein-Westfalen\', \'<p>Seit vielen Jahren entwickeln und betreiben die Kommunen in NRW mit ihren IT-Dienstleistern im KDN innovative IT-L&ouml;sungen. Das Angebot ist vielf&auml;ltig! Hier entsteht ein &Uuml;berblick:</p>

<ul>
	<li>Welche Online-Dienste gibt es bei Kommunen in NRW? Wer stellt diese bereit?</li>
	<li>Welche L&ouml;sungen sind schon im Betrieb? Welche haben erst Projekt-Status?</li>
	<li>Welche Angebote sind lokal, regional oder zentral f&uuml;r die Kommunen in NRW verf&uuml;gbar?</li>
</ul>

<p>Die Verwaltungssuchmaschine.NRW und der europaweite Portalverbund verkn&uuml;pfen alle Online-Dienste zu einem gezielten Gesamtangebot.</p>\', \'2020-05-22 10:11:48\', \'2020-05-22 10:11:48\');');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_implementation_project_funding DROP FOREIGN KEY FK_23C5C0179D70482');
        $this->addSql('DROP TABLE ozg_implementation_project_organisation_leaders');
        $this->addSql('DROP TABLE ozg_implementation_project_funding');
        $this->addSql('DROP TABLE ozg_funding');
        $this->addSql('DROP TABLE ozg_page_content');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions DROP position');
    }
}
