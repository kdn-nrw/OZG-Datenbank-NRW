<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220620132852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add onboarding form solution license confirmed field';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_form_solution ADD license_confirmed TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_form_solution_audit ADD license_confirmed TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_form_solution DROP license_confirmed');
        $this->addSql('ALTER TABLE ozg_onboarding_form_solution_audit DROP license_confirmed');
    }
}
