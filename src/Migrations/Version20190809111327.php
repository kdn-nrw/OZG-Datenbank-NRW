<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190809111327 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mb_email_email_template (id INT AUTO_INCREMENT NOT NULL, use_default_values TINYINT(1) NOT NULL, render_twig TINYINT(1) NOT NULL, subject VARCHAR(255) DEFAULT NULL, html LONGTEXT DEFAULT NULL, html_template_path VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, text_template_path VARCHAR(255) DEFAULT NULL, header_from LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_to LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_cc LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_bcc LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_reply_to LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_return_path VARCHAR(255) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', id_key VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3AA416B4143443F3 (id_key), INDEX mb_email_email_template_key_idx (id_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mb_email_email_log (id INT AUTO_INCREMENT NOT NULL, email_data_id INT DEFAULT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, recipient_hashes LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', sent_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', error_message VARCHAR(255) DEFAULT NULL, fail_count SMALLINT NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', anonymized_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX UNIQ_4C32742E8A35C9F3 (email_data_id), INDEX IDX_4C32742EDE12AB56 (created_by), INDEX IDX_4C32742E25F94802 (modified_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mb_email_email_data (id INT AUTO_INCREMENT NOT NULL, raw_email LONGTEXT NOT NULL, subject VARCHAR(255) DEFAULT NULL, body_text LONGTEXT DEFAULT NULL, body_html LONGTEXT DEFAULT NULL, header_from LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_to LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_cc LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_bcc LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_reply_to LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', header_return_path VARCHAR(255) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mb_email_email_log ADD CONSTRAINT FK_4C32742E8A35C9F3 FOREIGN KEY (email_data_id) REFERENCES mb_email_email_data (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mb_email_email_log ADD CONSTRAINT FK_4C32742EDE12AB56 FOREIGN KEY (created_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE mb_email_email_log ADD CONSTRAINT FK_4C32742E25F94802 FOREIGN KEY (modified_by) REFERENCES mb_user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE mb_setting_setting RENAME INDEX key_idx TO mb_setting_setting_key_idx');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mb_email_email_log DROP FOREIGN KEY FK_4C32742E8A35C9F3');
        $this->addSql('DROP TABLE mb_email_email_template');
        $this->addSql('DROP TABLE mb_email_email_log');
        $this->addSql('DROP TABLE mb_email_email_data');
        $this->addSql('ALTER TABLE mb_setting_setting RENAME INDEX mb_setting_setting_key_idx TO key_idx');
    }
}
