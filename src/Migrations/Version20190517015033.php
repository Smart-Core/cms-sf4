<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190517015033 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cms_domains (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_pid INT UNSIGNED DEFAULT NULL, language_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, is_redirect TINYINT(1) NOT NULL, paid_till_date DATE DEFAULT NULL, name VARCHAR(255) NOT NULL, comment LONGTEXT DEFAULT NULL, is_enabled TINYINT(1) DEFAULT \'1\', position SMALLINT DEFAULT 0, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D297B08C5E237E06 (name), INDEX IDX_D297B08C6EF3A788 (parent_pid), INDEX IDX_D297B08C82F1BAF4 (language_id), INDEX IDX_D297B08CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_folders (id INT UNSIGNED AUTO_INCREMENT NOT NULL, folder_pid INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, permissions_cache LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', title VARCHAR(255) NOT NULL, uri_part VARCHAR(255) DEFAULT NULL, is_file TINYINT(1) NOT NULL, meta LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', redirect_to VARCHAR(255) DEFAULT NULL, router_node_id INT DEFAULT NULL, lockout_nodes LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', template_inheritable VARCHAR(30) DEFAULT NULL, template_self VARCHAR(30) DEFAULT NULL, is_active TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, description LONGTEXT DEFAULT NULL, position SMALLINT DEFAULT 0, INDEX IDX_A0DBDC1EA640A07B (folder_pid), INDEX IDX_A0DBDC1EA76ED395 (user_id), INDEX IDX_A0DBDC1E1B5771DD (is_active), INDEX IDX_A0DBDC1E4AF38FD1 (deleted_at), INDEX IDX_A0DBDC1E462CE4F5 (position), UNIQUE INDEX UNIQ_A0DBDC1EA640A07B79628CD (folder_pid, uri_part), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_permissions_folders_read (folder_id INT UNSIGNED NOT NULL, user_group_id INT NOT NULL, INDEX IDX_136DA6CA162CB942 (folder_id), INDEX IDX_136DA6CA1ED93D47 (user_group_id), PRIMARY KEY(folder_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_permissions_folders_write (folder_id INT UNSIGNED NOT NULL, user_group_id INT NOT NULL, INDEX IDX_D5879FED162CB942 (folder_id), INDEX IDX_D5879FED1ED93D47 (user_group_id), PRIMARY KEY(folder_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_languages (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, code VARCHAR(12) NOT NULL, is_enabled TINYINT(1) DEFAULT \'1\', name VARCHAR(255) NOT NULL, position SMALLINT DEFAULT 0, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_63B7331477153098 (code), UNIQUE INDEX UNIQ_63B733145E237E06 (name), INDEX IDX_63B73314A76ED395 (user_id), INDEX IDX_63B7331446C53D4C (is_enabled), INDEX IDX_63B73314462CE4F5 (position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_modules (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, developer VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, bundle VARCHAR(255) NOT NULL, is_active TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_705B4CC6A57B32FD (bundle), INDEX IDX_705B4CC6A76ED395 (user_id), INDEX IDX_705B4CC61B5771DD (is_active), UNIQUE INDEX UNIQ_705B4CC65E237E0665FB8B9A (name, developer), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_nodes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, folder_id INT UNSIGNED DEFAULT NULL, region_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, controls_in_toolbar SMALLINT NOT NULL, module VARCHAR(50) NOT NULL, controller VARCHAR(255) DEFAULT NULL, params LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', template VARCHAR(30) DEFAULT NULL, permissions_cache LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', priority SMALLINT NOT NULL, is_cached TINYINT(1) NOT NULL, is_use_eip TINYINT(1) DEFAULT \'1\' NOT NULL, code_before VARCHAR(255) DEFAULT NULL, code_after VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, description LONGTEXT DEFAULT NULL, position SMALLINT DEFAULT 0, INDEX IDX_334E9ED0162CB942 (folder_id), INDEX IDX_334E9ED0A76ED395 (user_id), INDEX IDX_334E9ED01B5771DD (is_active), INDEX IDX_334E9ED04AF38FD1 (deleted_at), INDEX IDX_334E9ED0462CE4F5 (position), INDEX IDX_334E9ED098260155 (region_id), INDEX IDX_334E9ED0C242628 (module), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_permissions_nodes_read (node_id INT UNSIGNED NOT NULL, user_group_id INT NOT NULL, INDEX IDX_8D3D34AE460D9FD7 (node_id), INDEX IDX_8D3D34AE1ED93D47 (user_group_id), PRIMARY KEY(node_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_permissions_nodes_write (node_id INT UNSIGNED NOT NULL, user_group_id INT NOT NULL, INDEX IDX_9FC66A3E460D9FD7 (node_id), INDEX IDX_9FC66A3E1ED93D47 (user_group_id), PRIMARY KEY(node_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_permissions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, bundle VARCHAR(80) NOT NULL, action VARCHAR(80) NOT NULL, default_value TINYINT(1) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', position SMALLINT DEFAULT 0, created_at DATETIME NOT NULL, INDEX IDX_B00C7BBB462CE4F5 (position), INDEX IDX_B00C7BBBF4510C3A (default_value), UNIQUE INDEX UNIQ_B00C7BBBA57B32FD47CC8C92 (bundle, action), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_regions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, site_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, permissions_cache LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, position SMALLINT DEFAULT 0, INDEX IDX_FC8B76E2F6BD1646 (site_id), INDEX IDX_FC8B76E2A76ED395 (user_id), INDEX IDX_FC8B76E2462CE4F5 (position), UNIQUE INDEX region_name_in_site (name, site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_regions_inherit (region_id INT UNSIGNED NOT NULL, folder_id INT UNSIGNED NOT NULL, INDEX IDX_6392251D98260155 (region_id), INDEX IDX_6392251D162CB942 (folder_id), PRIMARY KEY(region_id, folder_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_permissions_regions_read (region_id INT UNSIGNED NOT NULL, user_group_id INT NOT NULL, INDEX IDX_C6CDDE1F98260155 (region_id), INDEX IDX_C6CDDE1F1ED93D47 (user_group_id), PRIMARY KEY(region_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_permissions_regions_write (region_id INT UNSIGNED NOT NULL, user_group_id INT NOT NULL, INDEX IDX_23EB19CE98260155 (region_id), INDEX IDX_23EB19CE1ED93D47 (user_group_id), PRIMARY KEY(region_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_sites (id INT UNSIGNED AUTO_INCREMENT NOT NULL, domain_id INT UNSIGNED DEFAULT NULL, root_folder_id INT UNSIGNED DEFAULT NULL, language_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, theme VARCHAR(255) DEFAULT NULL, web_root VARCHAR(255) DEFAULT NULL, is_enabled TINYINT(1) DEFAULT \'1\', name VARCHAR(255) NOT NULL, position SMALLINT DEFAULT 0, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9273314F5E237E06 (name), UNIQUE INDEX UNIQ_9273314F115F0EE5 (domain_id), UNIQUE INDEX UNIQ_9273314F5F3EA365 (root_folder_id), INDEX IDX_9273314F82F1BAF4 (language_id), INDEX IDX_9273314FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_syslog (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, bundle VARCHAR(255) NOT NULL, entity_id INT UNSIGNED NOT NULL, entity VARCHAR(255) NOT NULL, domain VARCHAR(255) DEFAULT NULL, old_value LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', new_value LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, ip_address VARCHAR(255) DEFAULT NULL, INDEX IDX_F3B4FDAFA76ED395 (user_id), INDEX IDX_F3B4FDAF8B8E8428 (created_at), INDEX IDX_F3B4FDAFA57B32FD (bundle), INDEX IDX_F3B4FDAFE284468 (entity), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', is_default_folders_granted_read TINYINT(1) NOT NULL, is_default_folders_granted_write TINYINT(1) NOT NULL, is_default_nodes_granted_read TINYINT(1) NOT NULL, is_default_nodes_granted_write TINYINT(1) NOT NULL, is_default_regions_granted_read TINYINT(1) NOT NULL, is_default_regions_granted_write TINYINT(1) NOT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_FF8AB7E05E237E06 (name), INDEX IDX_FF8AB7E0FF781009 (is_default_folders_granted_read), INDEX IDX_FF8AB7E0D7061951 (is_default_folders_granted_write), INDEX IDX_FF8AB7E03EB0FA8C (is_default_nodes_granted_read), INDEX IDX_FF8AB7E04A15A614 (is_default_nodes_granted_write), INDEX IDX_FF8AB7E0360B17FA (is_default_regions_granted_read), INDEX IDX_FF8AB7E0F37BC9F0 (is_default_regions_granted_write), INDEX IDX_FF8AB7E0462CE4F5 (position), INDEX IDX_FF8AB7E02B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_permissions_groups_relations (user_group_id INT NOT NULL, permission_id INT UNSIGNED NOT NULL, INDEX IDX_7563F9101ED93D47 (user_group_id), INDEX IDX_7563F910FED90CCA (permission_id), PRIMARY KEY(user_group_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cms_domains ADD CONSTRAINT FK_D297B08C6EF3A788 FOREIGN KEY (parent_pid) REFERENCES cms_domains (id)');
        $this->addSql('ALTER TABLE cms_domains ADD CONSTRAINT FK_D297B08C82F1BAF4 FOREIGN KEY (language_id) REFERENCES cms_languages (id)');
        $this->addSql('ALTER TABLE cms_domains ADD CONSTRAINT FK_D297B08CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cms_folders ADD CONSTRAINT FK_A0DBDC1EA640A07B FOREIGN KEY (folder_pid) REFERENCES cms_folders (id)');
        $this->addSql('ALTER TABLE cms_folders ADD CONSTRAINT FK_A0DBDC1EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cms_permissions_folders_read ADD CONSTRAINT FK_136DA6CA162CB942 FOREIGN KEY (folder_id) REFERENCES cms_folders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_folders_read ADD CONSTRAINT FK_136DA6CA1ED93D47 FOREIGN KEY (user_group_id) REFERENCES users_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_folders_write ADD CONSTRAINT FK_D5879FED162CB942 FOREIGN KEY (folder_id) REFERENCES cms_folders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_folders_write ADD CONSTRAINT FK_D5879FED1ED93D47 FOREIGN KEY (user_group_id) REFERENCES users_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_languages ADD CONSTRAINT FK_63B73314A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cms_modules ADD CONSTRAINT FK_705B4CC6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cms_nodes ADD CONSTRAINT FK_334E9ED0162CB942 FOREIGN KEY (folder_id) REFERENCES cms_folders (id)');
        $this->addSql('ALTER TABLE cms_nodes ADD CONSTRAINT FK_334E9ED098260155 FOREIGN KEY (region_id) REFERENCES cms_regions (id)');
        $this->addSql('ALTER TABLE cms_nodes ADD CONSTRAINT FK_334E9ED0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cms_permissions_nodes_read ADD CONSTRAINT FK_8D3D34AE460D9FD7 FOREIGN KEY (node_id) REFERENCES cms_nodes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_nodes_read ADD CONSTRAINT FK_8D3D34AE1ED93D47 FOREIGN KEY (user_group_id) REFERENCES users_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_nodes_write ADD CONSTRAINT FK_9FC66A3E460D9FD7 FOREIGN KEY (node_id) REFERENCES cms_nodes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_nodes_write ADD CONSTRAINT FK_9FC66A3E1ED93D47 FOREIGN KEY (user_group_id) REFERENCES users_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_regions ADD CONSTRAINT FK_FC8B76E2F6BD1646 FOREIGN KEY (site_id) REFERENCES cms_sites (id)');
        $this->addSql('ALTER TABLE cms_regions ADD CONSTRAINT FK_FC8B76E2A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cms_regions_inherit ADD CONSTRAINT FK_6392251D98260155 FOREIGN KEY (region_id) REFERENCES cms_regions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_regions_inherit ADD CONSTRAINT FK_6392251D162CB942 FOREIGN KEY (folder_id) REFERENCES cms_folders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_regions_read ADD CONSTRAINT FK_C6CDDE1F98260155 FOREIGN KEY (region_id) REFERENCES cms_regions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_regions_read ADD CONSTRAINT FK_C6CDDE1F1ED93D47 FOREIGN KEY (user_group_id) REFERENCES users_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_regions_write ADD CONSTRAINT FK_23EB19CE98260155 FOREIGN KEY (region_id) REFERENCES cms_regions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_regions_write ADD CONSTRAINT FK_23EB19CE1ED93D47 FOREIGN KEY (user_group_id) REFERENCES users_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_sites ADD CONSTRAINT FK_9273314F115F0EE5 FOREIGN KEY (domain_id) REFERENCES cms_domains (id)');
        $this->addSql('ALTER TABLE cms_sites ADD CONSTRAINT FK_9273314F5F3EA365 FOREIGN KEY (root_folder_id) REFERENCES cms_folders (id)');
        $this->addSql('ALTER TABLE cms_sites ADD CONSTRAINT FK_9273314F82F1BAF4 FOREIGN KEY (language_id) REFERENCES cms_languages (id)');
        $this->addSql('ALTER TABLE cms_sites ADD CONSTRAINT FK_9273314FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cms_syslog ADD CONSTRAINT FK_F3B4FDAFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cms_permissions_groups_relations ADD CONSTRAINT FK_7563F9101ED93D47 FOREIGN KEY (user_group_id) REFERENCES users_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cms_permissions_groups_relations ADD CONSTRAINT FK_7563F910FED90CCA FOREIGN KEY (permission_id) REFERENCES cms_permissions (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cms_domains DROP FOREIGN KEY FK_D297B08C6EF3A788');
        $this->addSql('ALTER TABLE cms_sites DROP FOREIGN KEY FK_9273314F115F0EE5');
        $this->addSql('ALTER TABLE cms_folders DROP FOREIGN KEY FK_A0DBDC1EA640A07B');
        $this->addSql('ALTER TABLE cms_permissions_folders_read DROP FOREIGN KEY FK_136DA6CA162CB942');
        $this->addSql('ALTER TABLE cms_permissions_folders_write DROP FOREIGN KEY FK_D5879FED162CB942');
        $this->addSql('ALTER TABLE cms_nodes DROP FOREIGN KEY FK_334E9ED0162CB942');
        $this->addSql('ALTER TABLE cms_regions_inherit DROP FOREIGN KEY FK_6392251D162CB942');
        $this->addSql('ALTER TABLE cms_sites DROP FOREIGN KEY FK_9273314F5F3EA365');
        $this->addSql('ALTER TABLE cms_domains DROP FOREIGN KEY FK_D297B08C82F1BAF4');
        $this->addSql('ALTER TABLE cms_sites DROP FOREIGN KEY FK_9273314F82F1BAF4');
        $this->addSql('ALTER TABLE cms_permissions_nodes_read DROP FOREIGN KEY FK_8D3D34AE460D9FD7');
        $this->addSql('ALTER TABLE cms_permissions_nodes_write DROP FOREIGN KEY FK_9FC66A3E460D9FD7');
        $this->addSql('ALTER TABLE cms_permissions_groups_relations DROP FOREIGN KEY FK_7563F910FED90CCA');
        $this->addSql('ALTER TABLE cms_nodes DROP FOREIGN KEY FK_334E9ED098260155');
        $this->addSql('ALTER TABLE cms_regions_inherit DROP FOREIGN KEY FK_6392251D98260155');
        $this->addSql('ALTER TABLE cms_permissions_regions_read DROP FOREIGN KEY FK_C6CDDE1F98260155');
        $this->addSql('ALTER TABLE cms_permissions_regions_write DROP FOREIGN KEY FK_23EB19CE98260155');
        $this->addSql('ALTER TABLE cms_regions DROP FOREIGN KEY FK_FC8B76E2F6BD1646');
        $this->addSql('ALTER TABLE cms_permissions_folders_read DROP FOREIGN KEY FK_136DA6CA1ED93D47');
        $this->addSql('ALTER TABLE cms_permissions_folders_write DROP FOREIGN KEY FK_D5879FED1ED93D47');
        $this->addSql('ALTER TABLE cms_permissions_nodes_read DROP FOREIGN KEY FK_8D3D34AE1ED93D47');
        $this->addSql('ALTER TABLE cms_permissions_nodes_write DROP FOREIGN KEY FK_9FC66A3E1ED93D47');
        $this->addSql('ALTER TABLE cms_permissions_regions_read DROP FOREIGN KEY FK_C6CDDE1F1ED93D47');
        $this->addSql('ALTER TABLE cms_permissions_regions_write DROP FOREIGN KEY FK_23EB19CE1ED93D47');
        $this->addSql('ALTER TABLE cms_permissions_groups_relations DROP FOREIGN KEY FK_7563F9101ED93D47');
        $this->addSql('DROP TABLE cms_domains');
        $this->addSql('DROP TABLE cms_folders');
        $this->addSql('DROP TABLE cms_permissions_folders_read');
        $this->addSql('DROP TABLE cms_permissions_folders_write');
        $this->addSql('DROP TABLE cms_languages');
        $this->addSql('DROP TABLE cms_modules');
        $this->addSql('DROP TABLE cms_nodes');
        $this->addSql('DROP TABLE cms_permissions_nodes_read');
        $this->addSql('DROP TABLE cms_permissions_nodes_write');
        $this->addSql('DROP TABLE cms_permissions');
        $this->addSql('DROP TABLE cms_regions');
        $this->addSql('DROP TABLE cms_regions_inherit');
        $this->addSql('DROP TABLE cms_permissions_regions_read');
        $this->addSql('DROP TABLE cms_permissions_regions_write');
        $this->addSql('DROP TABLE cms_sites');
        $this->addSql('DROP TABLE cms_syslog');
        $this->addSql('DROP TABLE users_groups');
        $this->addSql('DROP TABLE cms_permissions_groups_relations');
    }
}
