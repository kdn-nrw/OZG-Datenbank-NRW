<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200906170216 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM migration_versions WHERE version IN (\'20190709134342\', \'20190809111327\')');
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF2029E6B1585');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF2029E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested DROP FOREIGN KEY FK_86C0C7C42CC68C60');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested DROP FOREIGN KEY FK_86C0C7C49E6B1585');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested ADD CONSTRAINT FK_86C0C7C42CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested ADD CONSTRAINT FK_86C0C7C49E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation DROP FOREIGN KEY FK_DD2199D2CC68C60');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation DROP FOREIGN KEY FK_DD2199D9E6B1585');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation ADD CONSTRAINT FK_DD2199D2CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation ADD CONSTRAINT FK_DD2199D9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_service_solution DROP FOREIGN KEY FK_591B95281C0BE183');
        $this->addSql('ALTER TABLE ozg_service_solution DROP FOREIGN KEY FK_591B9528ED5CA9E6');
        $this->addSql('ALTER TABLE ozg_service_solution ADD CONSTRAINT FK_591B95281C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('ALTER TABLE ozg_service_solution ADD CONSTRAINT FK_591B9528ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_contact DROP FOREIGN KEY FK_2CCAF2029E6B1585');
        $this->addSql('ALTER TABLE ozg_contact ADD CONSTRAINT FK_2CCAF2029E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested DROP FOREIGN KEY FK_86C0C7C42CC68C60');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested DROP FOREIGN KEY FK_86C0C7C49E6B1585');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested ADD CONSTRAINT FK_86C0C7C42CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_interested ADD CONSTRAINT FK_86C0C7C49E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation DROP FOREIGN KEY FK_DD2199D2CC68C60');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation DROP FOREIGN KEY FK_DD2199D9E6B1585');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation ADD CONSTRAINT FK_DD2199D2CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_organisation_participation ADD CONSTRAINT FK_DD2199D9E6B1585 FOREIGN KEY (organisation_id) REFERENCES ozg_organisation (id)');
        $this->addSql('ALTER TABLE ozg_service_solution DROP FOREIGN KEY FK_591B9528ED5CA9E6');
        $this->addSql('ALTER TABLE ozg_service_solution DROP FOREIGN KEY FK_591B95281C0BE183');
        $this->addSql('ALTER TABLE ozg_service_solution ADD CONSTRAINT FK_591B9528ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_service_solution ADD CONSTRAINT FK_591B95281C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id) ON DELETE CASCADE');
    }
}
