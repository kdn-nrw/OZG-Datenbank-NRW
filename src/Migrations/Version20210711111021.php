<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210711111021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add data center tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_data_center (id INT AUTO_INCREMENT NOT NULL, service_provider_id INT DEFAULT NULL, operation_type INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, position INT DEFAULT NULL, joint_data_center_info LONGTEXT DEFAULT NULL, data_center_waste_heat TINYINT(1) NOT NULL, data_center_waste_heat_info LONGTEXT DEFAULT NULL, data_center_water_cooling TINYINT(1) NOT NULL, data_center_water_cooling_info LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8FDB9EEDC6C98E06 (service_provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_data_center_service_provider (data_center_id INT NOT NULL, service_provider_id INT NOT NULL, INDEX IDX_48F1AE43D69DB341 (data_center_id), INDEX IDX_48F1AE43C6C98E06 (service_provider_id), PRIMARY KEY(data_center_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_data_center_consumption (id INT AUTO_INCREMENT NOT NULL, data_center_id INT DEFAULT NULL, year INT DEFAULT NULL, power_consumption INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_3104FC73D69DB341 (data_center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_data_center ADD CONSTRAINT FK_8FDB9EEDC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_data_center_service_provider ADD CONSTRAINT FK_48F1AE43D69DB341 FOREIGN KEY (data_center_id) REFERENCES ozg_data_center (id)');
        $this->addSql('ALTER TABLE ozg_data_center_service_provider ADD CONSTRAINT FK_48F1AE43C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id)');
        $this->addSql('ALTER TABLE ozg_data_center_consumption ADD CONSTRAINT FK_3104FC73D69DB341 FOREIGN KEY (data_center_id) REFERENCES ozg_data_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_service_provider ADD data_center_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service_provider ADD CONSTRAINT FK_54ECCF6FD69DB341 FOREIGN KEY (data_center_id) REFERENCES ozg_data_center (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_54ECCF6FD69DB341 ON ozg_service_provider (data_center_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_data_center_service_provider DROP FOREIGN KEY FK_48F1AE43D69DB341');
        $this->addSql('ALTER TABLE ozg_data_center_consumption DROP FOREIGN KEY FK_3104FC73D69DB341');
        $this->addSql('ALTER TABLE ozg_service_provider DROP FOREIGN KEY FK_54ECCF6FD69DB341');
        $this->addSql('DROP TABLE ozg_data_center');
        $this->addSql('DROP TABLE ozg_data_center_service_provider');
        $this->addSql('DROP TABLE ozg_data_center_consumption');
        $this->addSql('DROP INDEX UNIQ_54ECCF6FD69DB341 ON ozg_service_provider');
        $this->addSql('ALTER TABLE ozg_service_provider DROP data_center_id');
    }
}
