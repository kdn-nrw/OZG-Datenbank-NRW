<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210524134803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable('ozg_onboarding_form_solution')) {
            $this->addSql('CREATE TABLE ozg_onboarding_form_solution (id INT NOT NULL, privacy_text LONGTEXT DEFAULT NULL, privacy_url VARCHAR(2048) DEFAULT NULL, imprint_text LONGTEXT DEFAULT NULL, imprint_url VARCHAR(2048) DEFAULT NULL, accessibility LONGTEXT DEFAULT NULL, opening_hours LONGTEXT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, letterhead_address LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE ozg_onboarding_form_solution ADD CONSTRAINT FK_A5D6F1A7BF396750 FOREIGN KEY (id) REFERENCES ozg_onboarding (id) ON DELETE CASCADE');
        }
        if (!$schema->getTable('ozg_commune')->hasColumn('administration_email')) {
        $this->addSql('ALTER TABLE ozg_commune ADD administration_email VARCHAR(255) DEFAULT NULL, ADD administration_phone_number VARCHAR(100) DEFAULT NULL, ADD administration_fax_number VARCHAR(100) DEFAULT NULL, ADD administration_url VARCHAR(2048) DEFAULT NULL');
        }
        if (!$schema->getTable('ozg_onboarding_contact')->hasColumn('commune_id')) {
            $this->addSql('ALTER TABLE ozg_onboarding_contact ADD commune_id INT DEFAULT NULL, ADD form_solution_id INT DEFAULT NULL');
            $this->addSql('ALTER TABLE ozg_onboarding_contact ADD CONSTRAINT FK_E6BDC8EA131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE ozg_onboarding_contact ADD CONSTRAINT FK_E6BDC8EAEB607192 FOREIGN KEY (form_solution_id) REFERENCES ozg_onboarding_form_solution (id) ON DELETE CASCADE');
            $this->addSql('CREATE INDEX IDX_E6BDC8EA131A4F72 ON ozg_onboarding_contact (commune_id)');
            $this->addSql('CREATE INDEX IDX_E6BDC8EAEB607192 ON ozg_onboarding_contact (form_solution_id)');
        }

        $this->addSql("UPDATE ozg_onboarding_contact c, ozg_onboarding o
            SET c.commune_id = o.commune_id
            WHERE o.record_type = 'epayment' AND c.epayment_id = o.id");
        $this->addSql("UPDATE ozg_onboarding_contact c, ozg_onboarding o
            SET c.commune_id = o.commune_id
            WHERE o.record_type = 'serviceaccount' AND c.service_account_id = o.id");
        $this->addSql("UPDATE ozg_onboarding_contact c, ozg_onboarding o
            SET c.commune_id = o.commune_id
            WHERE o.record_type = 'communeinfo' AND c.commune_info_id = o.id");
        $this->addSql("UPDATE ozg_onboarding_contact c, ozg_onboarding fs, ozg_onboarding bi
            SET c.form_solution_id = fs.id, c.commune_id = bi.commune_id
            WHERE fs.record_type = 'formsolution'
            AND bi.record_type = 'communeinfo' AND c.commune_info_id = bi.id
            AND fs.commune_id = bi.commune_id AND c.form_solution_id IS NULL AND c.contact_type = 'fs'");
        $this->addSql("UPDATE ozg_onboarding_contact c, ozg_onboarding fs, ozg_onboarding bi
            SET c.commune_info_id = bi.id, c.commune_id = bi.commune_id
            WHERE fs.record_type = 'formsolution'
            AND bi.record_type = 'communeinfo' AND c.commune_info_id IS NULL
            AND fs.commune_id = bi.commune_id AND c.form_solution_id = fs.id AND c.contact_type = 'fs'");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP FOREIGN KEY FK_E6BDC8EAEB607192');
        $this->addSql('DROP TABLE ozg_onboarding_form_solution');
        $this->addSql('ALTER TABLE ozg_commune DROP administration_email, DROP administration_phone_number, DROP administration_fax_number, DROP administration_url');
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP FOREIGN KEY FK_E6BDC8EA131A4F72');
        $this->addSql('DROP INDEX IDX_E6BDC8EA131A4F72 ON ozg_onboarding_contact');
        $this->addSql('DROP INDEX IDX_E6BDC8EAEB607192 ON ozg_onboarding_contact');
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP commune_id, DROP form_solution_id');
    }
}
