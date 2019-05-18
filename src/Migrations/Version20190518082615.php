<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190518082615 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE texter_items (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, locale VARCHAR(8) DEFAULT NULL, editor SMALLINT NOT NULL, meta LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, text LONGTEXT DEFAULT NULL, INDEX IDX_CD3DA84A76ED395 (user_id), INDEX IDX_CD3DA848B8E8428 (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE texter_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, locale VARCHAR(8) NOT NULL, editor SMALLINT NOT NULL, meta LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, text LONGTEXT DEFAULT NULL, INDEX IDX_82529097126F525E (item_id), INDEX IDX_82529097A76ED395 (user_id), INDEX IDX_825290974AF38FD1 (deleted_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE texter_items ADD CONSTRAINT FK_CD3DA84A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE texter_history ADD CONSTRAINT FK_82529097126F525E FOREIGN KEY (item_id) REFERENCES texter_items (id)');
        $this->addSql('ALTER TABLE texter_history ADD CONSTRAINT FK_82529097A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE texter_history DROP FOREIGN KEY FK_82529097126F525E');
        $this->addSql('DROP TABLE texter_items');
        $this->addSql('DROP TABLE texter_history');
    }
}
