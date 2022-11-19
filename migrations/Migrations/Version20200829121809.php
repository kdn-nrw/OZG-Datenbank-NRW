<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200829121809 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_search_index_word ADD is_generated TINYINT(1) NOT NULL');
        $this->addSql('CREATE INDEX search_baseword ON ozg_search_index_word (baseword)');
        $this->addSql('CREATE INDEX search_stopword ON ozg_search_index_word (is_stopword)');
        $this->addSql('CREATE INDEX search_generated ON ozg_search_index_word (is_generated)');
        $this->addSql('CREATE INDEX search_phonetic ON ozg_search_index_word (metaphone)');
        $this->addSql('ALTER TABLE cron_job ADD running_instances INT UNSIGNED DEFAULT 0 NOT NULL, ADD max_instances INT UNSIGNED DEFAULT 1 NOT NULL, CHANGE number number INT UNSIGNED DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX search_baseword ON ozg_search_index_word');
        $this->addSql('DROP INDEX search_stopword ON ozg_search_index_word');
        $this->addSql('DROP INDEX search_generated ON ozg_search_index_word');
        $this->addSql('DROP INDEX search_phonetic ON ozg_search_index_word');
        $this->addSql('ALTER TABLE ozg_search_index_word DROP is_generated');
    }
}
