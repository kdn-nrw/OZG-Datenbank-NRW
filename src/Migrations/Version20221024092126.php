<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024092126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ePayBL payment types';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('ozg_onboarding_epayment')->hasColumn('ozg_onboarding_epayment')) {
            $this->addSql('ALTER TABLE ozg_onboarding_epayment ADD payment_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
            $this->addSql('ALTER TABLE ozg_onboarding_epayment_audit ADD payment_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ($schema->getTable('ozg_onboarding_epayment')->hasColumn('ozg_onboarding_epayment')) {
            $this->addSql('ALTER TABLE ozg_onboarding_epayment DROP payment_types');
            $this->addSql('ALTER TABLE ozg_onboarding_epayment_audit DROP payment_types');
        }
    }
}
