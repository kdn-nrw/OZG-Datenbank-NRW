<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221101194423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ZuFi API commune service detail information';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('ozg_api_service_base_result')->hasColumn('commune_author')) {
            $this->addSql('ALTER TABLE ozg_api_service_base_result ADD commune_author VARCHAR(255) DEFAULT NULL, ADD commune_has_details TINYINT(1) DEFAULT NULL, ADD commune_wsp_relevance TINYINT(1) DEFAULT NULL, ADD commune_last_updated_at VARCHAR(255) DEFAULT NULL, ADD commune_online_service_url_info VARCHAR(255) DEFAULT NULL, ADD commune_office_name VARCHAR(255) DEFAULT NULL, CHANGE performance performance VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE service_type service_type VARCHAR(255) DEFAULT NULL, CHANGE date date VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ($schema->getTable('ozg_api_service_base_result')->hasColumn('commune_author')) {
            $this->addSql('ALTER TABLE ozg_api_service_base_result DROP commune_author, DROP commune_has_details, DROP commune_wsp_relevance, DROP commune_last_updated_at, DROP commune_online_service_url_info, DROP commune_office_name, CHANGE performance performance LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type type LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE service_type service_type LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE date date LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        }
    }
}
