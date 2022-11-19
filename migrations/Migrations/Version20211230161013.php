<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211230161013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_model_region_concept_query_type (id INT AUTO_INCREMENT NOT NULL, query_group INT NOT NULL, description LONGTEXT DEFAULT NULL, placeholder LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, position INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_model_region_project_concept_query (id INT AUTO_INCREMENT NOT NULL, model_region_project_id INT DEFAULT NULL, concept_query_type_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_5092F38C127D04D2 (model_region_project_id), INDEX IDX_5092F38CDD1405E9 (concept_query_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_model_region_project_concept_query ADD CONSTRAINT FK_5092F38C127D04D2 FOREIGN KEY (model_region_project_id) REFERENCES ozg_model_region_project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_model_region_project_concept_query ADD CONSTRAINT FK_5092F38CDD1405E9 FOREIGN KEY (concept_query_type_id) REFERENCES ozg_model_region_concept_query_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_model_region_project_concept_query ADD position INT DEFAULT NULL, DROP name, DROP hidden');
        $this->addSql('INSERT INTO `ozg_model_region_concept_query_type` (id, query_group, description, modified_at, created_at, name, hidden, position, placeholder) VALUES (1,1,NULL,\'2022-01-04 13:38:16\',\'2022-01-04 13:38:16\',\'Für wen ist das Projekt zur Nachnutzung interessant?\',0,1,NULL),
(2,1,\'Bürger:innen, Behörde, Institutionen wie Schulen etc., Wirtschaft, weitere Akteure\',\'2022-01-04 13:38:34\',\'2022-01-04 13:38:34\',\'Wer profitiert aus Sicht des Projektbüros am meisten von diesem Projekt?\',0,2,NULL),
(3,2,NULL,\'2022-01-04 13:46:07\',\'2022-01-04 13:38:43\',\'Welche Konzepte sind nachnutzbar?\',0,3,\'Als Konzepte werden nachnutzbare Elemente bezeichnet, die die Planung zukünftiger Projekte erleichtern und deren Herangehensweise prägen (z.B. nachnutzbare Vorlagen in Form von Formularen, Profilen und Referenzprozessen.\'),
(4,2,\'Welche frei verfügbaren Lösungen werden im Zuge des Projektes bereitgestellt?\',\'2022-01-04 13:46:19\',\'2022-01-04 13:38:56\',\'Lösungsumfang\',0,4,\'Frei verfügbare Lösungen - die in Form von standardisierten, lizenzfreien und ggf. open-source Komponenten, perspektivische Initiativen unterstützen\'),
(5,2,NULL,\'2022-01-04 13:39:07\',\'2022-01-04 13:39:07\',\'Wann werden welche Elemente zur Verfügung gestellt?\',0,5,NULL),
(6,2,\'Links z.B. der Stadthomepage, oder weiteren Onlineportalen github\',\'2022-01-04 13:39:24\',\'2022-01-04 13:39:24\',\'Wie geschieht die Zur-Verfügung-Stellung?\',0,6,NULL),
(7,3,\'z.B. Welche Infrastruktur muss bereits vorhanden sein?\',\'2022-01-04 13:39:46\',\'2022-01-04 13:39:46\',\'Welche Beteiligungsrechte sind zu bedenken?\',0,7,NULL),
(8,3,\'z.B. Welche Gremien müssen dem Projekt zustimmen?\',\'2022-01-04 13:39:58\',\'2022-01-04 13:39:58\',\'Organisatorische Voraussetzungen\',0,8,NULL),
(9,4,NULL,\'2022-01-04 13:46:29\',\'2022-01-04 13:40:06\',\'Kosten der Einführung\',0,9,\'Bitte geben Sie eine Spannbreite an (z.B. 1 000 – 10 000 €).\'),
(10,4,\'Angabe jährlicher Kosten\',\'2022-01-04 13:46:43\',\'2022-01-04 13:40:18\',\'Kosten des Betriebs\',0,10,\'Bitte geben Sie eine Spannbreite an (z.B. 1 000 – 10 000 € pro Jahr).\'),
(11,1,\'Personentage und Zeitraum\',\'2022-01-04 13:46:52\',\'2022-01-04 13:40:40\',\'Personalaufwand bei Einführung\',0,11,\'Bitte geben Sie den Aufwand auf Seiten des Mandanten (z.B. Kommune) in Vollzeitäquivalenten und Monaten an (100 Personentage über Monate.\'),
(12,4,\'Angabe jährlicher Kosten\',\'2022-01-04 13:47:04\',\'2022-01-04 13:41:01\',\'Personalaufwand bei Betrieb\',0,12,\'Bitte geben Sie den Aufwand auf Seiten des Mandanten (z.B. Kommune) in Vollzeitäquivalenten pro Jahr an.\'),
(13,1,NULL,\'2022-01-04 13:41:10\',\'2022-01-04 13:41:10\',\'Welche spezifischen Kenntnisse sind hierzu erforderlich?\',0,13,NULL),
(14,4,NULL,\'2022-01-04 13:41:19\',\'2022-01-04 13:41:19\',\'Mögliche Finanzierungsquellen\',0,14,NULL),
(15,5,NULL,\'2022-01-04 13:47:13\',\'2022-01-04 13:41:29\',\'Direkter Nutzen / Einsparungen\',0,15,\'Bitte berücksichtigen Sie hier den direkten Nutzen für den Mandanten (z.B. Kommune).\'),
(16,5,NULL,\'2022-01-04 13:41:35\',\'2022-01-04 13:41:35\',\'Indirekte Einsparungen\',0,16,NULL),
(17,5,\'z.B. Welche Folgeprojekte werden ermöglicht?\',\'2022-01-04 13:41:47\',\'2022-01-04 13:41:47\',\'Langfristiger Nutzen\',0,17,NULL),
(18,1,\'z.B. Nutzen, der nur für Bürger:innen / Unternehmen / … anfällt\',\'2022-01-04 13:42:19\',\'2022-01-04 13:42:19\',\'Nutzen für Stakeholder\',0,18,NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_model_region_project_concept_query DROP FOREIGN KEY FK_5092F38CDD1405E9');
        $this->addSql('DROP TABLE ozg_model_region_concept_query_type');
        $this->addSql('DROP TABLE ozg_model_region_project_concept_query');
    }
}
