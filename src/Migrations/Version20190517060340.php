<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190517060340 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE settings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, bundle VARCHAR(32) NOT NULL, category VARCHAR(255) DEFAULT NULL, name VARCHAR(64) NOT NULL, value LONGTEXT DEFAULT NULL, is_serialized TINYINT(1) NOT NULL, is_hidden TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E545A0C564C19C1 (category), UNIQUE INDEX UNIQ_E545A0C5A57B32FD5E237E06 (bundle, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, setting_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, is_personal TINYINT(1) DEFAULT \'0\' NOT NULL, value LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_99BED87BEE35BD72 (setting_id), INDEX IDX_99BED87BA76ED395 (user_id), INDEX IDX_99BED87B64231B80 (is_personal), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings_personal (id INT UNSIGNED AUTO_INCREMENT NOT NULL, setting_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, value LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D7ED5980EE35BD72 (setting_id), INDEX IDX_D7ED5980A76ED395 (user_id), UNIQUE INDEX UNIQ_D7ED5980EE35BD72A76ED395 (setting_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE settings_history ADD CONSTRAINT FK_99BED87BEE35BD72 FOREIGN KEY (setting_id) REFERENCES settings (id)');
        $this->addSql('ALTER TABLE settings_history ADD CONSTRAINT FK_99BED87BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE settings_personal ADD CONSTRAINT FK_D7ED5980EE35BD72 FOREIGN KEY (setting_id) REFERENCES settings (id)');
        $this->addSql('ALTER TABLE settings_personal ADD CONSTRAINT FK_D7ED5980A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE settings_history DROP FOREIGN KEY FK_99BED87BEE35BD72');
        $this->addSql('ALTER TABLE settings_personal DROP FOREIGN KEY FK_D7ED5980EE35BD72');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE settings_history');
        $this->addSql('DROP TABLE settings_personal');
    }
}
