<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Behat\Transliterator\Transliterator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201015214949 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // this up() migration is auto-generated, please modify it to your needs
        $slugTables = [
            'ozg_commune' => ["name"],
        ];
        foreach ($slugTables as $table => $slugFields) {
            $tableDefinition = $schema->getTable('ozg_commune');
            $slugMap = [];
            $rows = $this->connection->fetchAll('SELECT id, '.implode(', ', $slugFields).' FROM ' . $table . ' ORDER BY id ASC');
            if (!$tableDefinition->hasColumn('slug')) {
                $this->addSql('ALTER TABLE '.$table.' ADD slug VARCHAR(128) NOT NULL');
            }
            foreach ($rows as $row) {
                $slug = $this->getRowSlug($row, $slugMap);
                $id = (int) $row['id'];
                $this->addSql('UPDATE '.$table.' SET slug = \''.$slug.'\' WHERE id = ' . $id);
            }
        }
        $this->addSql('CREATE UNIQUE INDEX UNIQ_824AC5D4989D9B62 ON ozg_commune (slug)');
        $this->addSql('ALTER TABLE ozg_commune RENAME INDEX fk_824ac5d4693b626f TO IDX_824AC5D4693B626F');
        $this->addSql('ALTER TABLE ozg_service ADD inherit_commune_types TINYINT(1) DEFAULT NULL');
        $this->addSql('UPDATE ozg_service SET inherit_commune_types = 1 WHERE id > 0');
    }

    /**
     * Create a unique slug for the given row
     *
     * @param array $row
     * @param array $slugMap The list of already generated slugs
     * @param int $maxLength The maximum length for the slug
     * @return string
     */
    private function getRowSlug(array $row, array &$slugMap, int $maxLength = 128): string
    {
        $id = $row['id'] ?? 0;
        unset($row['id']);
        $text = implode(' ', array_filter($row));
        $slug = $this->urlize($text);
        /** @noinspection NotOptimalIfConditionsInspection */
        if (in_array($slug, $slugMap, false) && $id) {
            $slug = $this->urlize($text. '-' . (int) $id);
        }
        if (in_array($slug, $slugMap, false)) {
            $slug = $this->urlize($text);
            $tmpSlug = $slug;
            if (strlen($tmpSlug) > $maxLength - 4) {
                $tmpSlug = substr($tmpSlug, 0, $maxLength - 4);
            }
            $offset = 1;
            while ($offset < 10000 && in_array($slug, $slugMap, false)) {
                $slug = $tmpSlug . '-' . $offset;
                ++$offset;
            }
        }
        $slugMap[] = $slug;
        return $slug;
    }

    /**
     * Create url from given text; crop to max length if necessary
     *
     * @param string $text
     * @param int $maxLength
     * @return string
     */
    private function urlize(string $text, int $maxLength = 128): string
    {
        $slug = Transliterator::urlize($text);
        if (strlen($slug) > $maxLength) {
            $slug = substr($slug, 0, $maxLength);
        }
        return $slug;
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_824AC5D4989D9B62 ON ozg_commune');
        $this->addSql('ALTER TABLE ozg_commune DROP slug');
        $this->addSql('ALTER TABLE ozg_commune RENAME INDEX idx_824ac5d4693b626f TO FK_824AC5D4693B626F');
        $this->addSql('ALTER TABLE ozg_service DROP commune_types, DROP inherit_commune_types');
        $this->addSql('ALTER TABLE ozg_service_system DROP commune_types');
    }
}
