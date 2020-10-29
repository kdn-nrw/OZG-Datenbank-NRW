<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201029112900 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_efile_status ADD prev_status_id INT DEFAULT NULL, ADD next_status_id INT DEFAULT NULL, ADD color VARCHAR(8) DEFAULT NULL, ADD css_class VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_efile_status ADD CONSTRAINT FK_E5C828A882FB4933 FOREIGN KEY (prev_status_id) REFERENCES ozg_efile_status (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_efile_status ADD CONSTRAINT FK_E5C828A82C7DD58E FOREIGN KEY (next_status_id) REFERENCES ozg_efile_status (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_E5C828A882FB4933 ON ozg_efile_status (prev_status_id)');
        $this->addSql('CREATE INDEX IDX_E5C828A82C7DD58E ON ozg_efile_status (next_status_id)');
        $this->addSql('ALTER TABLE ozg_implementation_status ADD color VARCHAR(8) DEFAULT NULL, ADD css_class VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_import ADD service_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_priority ADD prev_priority_id INT DEFAULT NULL, ADD next_priority_id INT DEFAULT NULL, ADD color VARCHAR(8) DEFAULT NULL, ADD css_class VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_priority ADD CONSTRAINT FK_A4CAAD81E37BBC04 FOREIGN KEY (prev_priority_id) REFERENCES ozg_priority (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_priority ADD CONSTRAINT FK_A4CAAD814BA91D18 FOREIGN KEY (next_priority_id) REFERENCES ozg_priority (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_A4CAAD81E37BBC04 ON ozg_priority (prev_priority_id)');
        $this->addSql('CREATE INDEX IDX_A4CAAD814BA91D18 ON ozg_priority (next_priority_id)');
        $this->addSql('ALTER TABLE ozg_status ADD prev_status_id INT DEFAULT NULL, ADD next_status_id INT DEFAULT NULL, ADD color VARCHAR(8) DEFAULT NULL, ADD css_class VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_status ADD CONSTRAINT FK_639A8A8382FB4933 FOREIGN KEY (prev_status_id) REFERENCES ozg_status (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_status ADD CONSTRAINT FK_639A8A832C7DD58E FOREIGN KEY (next_status_id) REFERENCES ozg_status (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_639A8A8382FB4933 ON ozg_status (prev_status_id)');
        $this->addSql('CREATE INDEX IDX_639A8A832C7DD58E ON ozg_status (next_status_id)');
        $this->addSql('ALTER TABLE ozg_maturity ADD color VARCHAR(8) DEFAULT NULL, ADD css_class VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_efile_status DROP FOREIGN KEY FK_E5C828A882FB4933');
        $this->addSql('ALTER TABLE ozg_efile_status DROP FOREIGN KEY FK_E5C828A82C7DD58E');
        $this->addSql('DROP INDEX IDX_E5C828A882FB4933 ON ozg_efile_status');
        $this->addSql('DROP INDEX IDX_E5C828A82C7DD58E ON ozg_efile_status');
        $this->addSql('ALTER TABLE ozg_efile_status DROP prev_status_id, DROP next_status_id, DROP color, DROP css_class');
        $this->addSql('ALTER TABLE ozg_implementation_status DROP color, DROP css_class');
        $this->addSql('ALTER TABLE ozg_import DROP service_id');
        $this->addSql('ALTER TABLE ozg_priority DROP FOREIGN KEY FK_A4CAAD81E37BBC04');
        $this->addSql('ALTER TABLE ozg_priority DROP FOREIGN KEY FK_A4CAAD814BA91D18');
        $this->addSql('DROP INDEX IDX_A4CAAD81E37BBC04 ON ozg_priority');
        $this->addSql('DROP INDEX IDX_A4CAAD814BA91D18 ON ozg_priority');
        $this->addSql('ALTER TABLE ozg_priority DROP prev_priority_id, DROP next_priority_id, DROP color, DROP css_class');
        $this->addSql('ALTER TABLE ozg_status DROP FOREIGN KEY FK_639A8A8382FB4933');
        $this->addSql('ALTER TABLE ozg_status DROP FOREIGN KEY FK_639A8A832C7DD58E');
        $this->addSql('DROP INDEX IDX_639A8A8382FB4933 ON ozg_status');
        $this->addSql('DROP INDEX IDX_639A8A832C7DD58E ON ozg_status');
        $this->addSql('ALTER TABLE ozg_status DROP prev_status_id, DROP next_status_id, DROP color, DROP css_class');
        $this->addSql('ALTER TABLE ozg_maturity DROP color, DROP css_class');
    }
}
