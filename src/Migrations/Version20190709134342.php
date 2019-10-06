<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190709134342 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mb_setting_setting (id INT AUTO_INCREMENT NOT NULL, default_value TINYINT(1) NOT NULL, value LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', id_key VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX UNIQ_82E4ABF6143443F3 (id_key), INDEX key_idx (id_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mb_log_sys_log (id INT AUTO_INCREMENT NOT NULL, channel VARCHAR(255) NOT NULL, level INT NOT NULL, level_name VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, formatted LONGTEXT DEFAULT NULL, context LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_server_authentication ADD deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_server ADD deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_user_key ADD deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_server ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE mb_sshkeymanagement_server SET name=host WHERE name IS NULL');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_server_authentication ADD last_synchronization_error_message VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mb_setting_setting');
        $this->addSql('DROP TABLE mb_log_sys_log');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_server DROP deleted_at');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_server_authentication DROP deleted_at');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_user_key DROP deleted_at');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_server DROP name');
        $this->addSql('ALTER TABLE mb_sshkeymanagement_server_authentication DROP last_synchronization_error_message');
    }
}
