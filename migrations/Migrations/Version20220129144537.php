<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220129144537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_api_service_base_result CHANGE technically_approved_by technically_approved_by LONGTEXT DEFAULT NULL');
        if (!$schema->getTable('ozg_model_region_concept_query_type')->hasColumn('choices_text')) {
            $this->addSql('ALTER TABLE ozg_model_region_concept_query_type ADD choices_text LONGTEXT DEFAULT NULL');
        }
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (19,21,NULL,NULL,\'2022-01-29 14:34:27\',\'2022-01-29 14:34:27\',\'Welche rechtlichen Rahmenbedingungen wurden analysiert (z.B. hinsichtlich Lizensierungsüberlegungen, Genehmigungsverfahren, etc.)\',0,22,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (20,21,NULL,NULL,\'2022-01-29 14:34:39\',\'2022-01-29 14:34:39\',\'Welche rechtlichen Hürden sind aufgetreten? Wie konnten diese gelöst werden?\',0,23,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (21,22,\'Was kann anderen Anwendern empfohlen werden?\',NULL,\'2022-01-29 14:34:56\',\'2022-01-29 14:34:56\',\'Best Practices\',0,23,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (22,22,\'Was kann nicht empfohlen werden? Was sollte vermieden werden?\',NULL,\'2022-01-29 14:35:13\',\'2022-01-29 14:35:13\',\'Lessons Learned\',0,24,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (23,31,NULL,NULL,\'2022-01-29 14:35:34\',\'2022-01-29 14:35:34\',\'Welche Teilprojekte gibt es und hat sich diese Einteilung bewährt?\',0,32,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (24,32,\'ggf. Zeitplan im Anhang\',NULL,\'2022-01-29 14:35:50\',\'2022-01-29 14:35:50\',\'Gesamtzeitübersicht des Projektes\',0,33,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (25,32,NULL,NULL,\'2022-01-29 14:36:00\',\'2022-01-29 14:36:00\',\'Projektphasen und Meilensteine\',0,34,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (26,32,NULL,NULL,\'2022-01-29 14:36:13\',\'2022-01-29 14:36:13\',\'Dauer von erster Überlegung zu Beschluss über Projektbeginn bis hin zu Projektabschluss / Betriebsaufnahme\',0,35,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (27,33,\'ggf. Projektorganigramm im Anhang\',NULL,\'2022-01-29 14:36:31\',\'2022-01-29 14:36:31\',\'Wie ist das Projektteam aufgebaut?\',0,34,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (28,33,NULL,NULL,\'2022-01-29 14:36:43\',\'2022-01-29 14:36:43\',\'Welche Rollen gibt es im Projekt?\',0,35,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (29,33,NULL,NULL,\'2022-01-29 14:36:55\',\'2022-01-29 14:36:55\',\'Welche spezifischen Kenntnisse sind erforderlich?\',0,36,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (30,33,\'VZÄ für wie viele Monate  aufgeschlüsselt nach Akteuren\',NULL,\'2022-01-29 14:37:16\',\'2022-01-29 14:37:16\',\'Wie hoch ist der Personalaufwand?\',0,37,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (31,33,NULL,NULL,\'2022-01-29 14:37:25\',\'2022-01-29 14:37:25\',\'Wie verändern sich die Personalanforderungen beim Übergang von Projekt zu Regelbetrieb?\',0,38,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (32,33,NULL,NULL,\'2022-01-29 14:37:35\',\'2022-01-29 14:37:35\',\'Welche Verwaltungsebenen/Stellen müssen einbezogen werden?\',0,39,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (33,33,\'z.B. Kommune aus\',NULL,\'2022-01-29 14:50:56\',\'2022-01-29 14:38:06\',\'Wie sieht das Modell zur Beauftragung bzw. zur Zusammenarbeit zwischen privatwirtschaftlichen Akteuren und Mandanten\',0,40,\'Konventionelle Beschaffung\r\nBetriebsvertrag/Lizensierung\r\nLangzeit – Leasing\r\nJoint Venture\r\nÖffentlich-Private-Partnerschaft (ÖPP)\r\nFranchising\r\nPrivatisierung/Verkauf\')');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (34,33,NULL,NULL,\'2022-01-29 14:56:02\',\'2022-01-29 14:38:17\',\'Im Falle von konventioneller Beschaffung\',0,8,\'Offenes/Nicht-offenes Verfahren\r\nVerhandlungsverfahren\r\nWettbewerblicher Dialog\r\nInnovationspartnerschaft\')');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (35,34,\'Welche Hürden müssen vor Projektbeginn überwunden werden? Welche Lösungsansätze wurden gewählt?\',NULL,\'2022-01-29 14:38:36\',\'2022-01-29 14:38:36\',\'Ex Ante\',0,35,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (36,34,\'Welche Herausforderungen gab es während des Projektverlaufs? Welche Lösungsansätze wurden gewählt?\',NULL,\'2022-01-29 14:38:51\',\'2022-01-29 14:38:51\',\'Laufend\',0,36,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (37,34,\'Welche Herausforderungen mit Hinblick auf den Betrieb sind aufgetreten, z.B. Akzeptanz der Lösung, Betriebsverantwortlichkeit, Finanzierung des Betriebs? Welche Lösungsansätze wurden gewählt?\',NULL,\'2022-01-29 14:39:21\',\'2022-01-29 14:39:21\',\'Ex Post\',0,37,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (38,35,NULL,NULL,\'2022-01-29 14:39:31\',\'2022-01-29 14:39:31\',\'Gab es zu den gewählten Lösungswegen betrachtete Alternativen?\',0,36,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (39,35,NULL,NULL,\'2022-01-29 14:39:40\',\'2022-01-29 14:39:40\',\'Welche Alternativen sind für Mandanten (z.B. Kommunen) empfehlenswert?\',0,37,NULL)');
        $this->addSql('REPLACE INTO `ozg_model_region_concept_query_type` (`id`, `query_group`, `description`, `placeholder`, `modified_at`, `created_at`, `name`, `hidden`, `position`, `choices_text`) VALUES (40,41,NULL,NULL,\'2022-01-29 14:39:50\',\'2022-01-29 14:39:50\',\'Haben Sie weitere Kommentare oder Anregungen?\',0,42,NULL)');
        $this->addSql('UPDATE ozg_model_region_concept_query_type SET position = id WHERE id > 18');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_api_service_base_result CHANGE technically_approved_by technically_approved_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_model_region_concept_query_type DROP choices_text');
    }
}
