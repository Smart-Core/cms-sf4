<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190522080450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE example (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE cms_appearance_history');
        $this->addSql('DROP TABLE sitemap_urls');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cms_appearance_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, filename VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, code LONGTEXT NOT NULL COLLATE utf8_unicode_ci, hash VARCHAR(32) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, INDEX IDX_C7EFEB7DD1B862B8 (hash), INDEX IDX_C7EFEB7D3C0BE965 (filename), INDEX IDX_C7EFEB7DA76ED395 (user_id), INDEX IDX_C7EFEB7DB548B0F (path), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sitemap_urls (id INT AUTO_INCREMENT NOT NULL, is_visited TINYINT(1) NOT NULL, loc VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, referer VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, title_hash VARCHAR(32) DEFAULT NULL COLLATE utf8_unicode_ci, title LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, lastmod DATETIME DEFAULT NULL, changefreq VARCHAR(8) DEFAULT NULL COLLATE utf8_unicode_ci, priority DOUBLE PRECISION DEFAULT NULL, status SMALLINT NOT NULL, INDEX IDX_365093829A62B8C7 (title_hash), UNIQUE INDEX UNIQ_365093828852ACDC (loc), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cms_appearance_history ADD CONSTRAINT FK_C7EFEB7DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('DROP TABLE example');
    }
}
