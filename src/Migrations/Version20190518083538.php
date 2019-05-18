<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190518083538 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE media_categories (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_id INT UNSIGNED DEFAULT NULL, slug VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL, title VARCHAR(255) DEFAULT NULL, INDEX IDX_30D688FC727ACA70 (parent_id), INDEX IDX_30D688FC989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_collections (id INT UNSIGNED AUTO_INCREMENT NOT NULL, default_storage_id INT UNSIGNED NOT NULL, default_filter VARCHAR(255) DEFAULT NULL, params LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', relative_path VARCHAR(255) NOT NULL, file_relative_path_pattern VARCHAR(255) NOT NULL, filename_pattern VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL, title VARCHAR(255) DEFAULT NULL, INDEX IDX_244DA17D14E68FF3 (default_storage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_files (id INT UNSIGNED AUTO_INCREMENT NOT NULL, collection_id INT UNSIGNED NOT NULL, category_id INT UNSIGNED DEFAULT NULL, storage_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, is_preuploaded TINYINT(1) NOT NULL, relative_path VARCHAR(100) DEFAULT NULL, filename VARCHAR(100) NOT NULL, original_filename VARCHAR(255) NOT NULL, type VARCHAR(8) NOT NULL, mime_type VARCHAR(32) NOT NULL, original_size INT NOT NULL, size INT NOT NULL, created_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_192C84E8514956FD (collection_id), INDEX IDX_192C84E812469DE2 (category_id), INDEX IDX_192C84E85CC5DB90 (storage_id), INDEX IDX_192C84E8A76ED395 (user_id), INDEX IDX_192C84E8F7C0246A (size), INDEX IDX_192C84E88CDE5729 (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_files_transformed (id INT UNSIGNED AUTO_INCREMENT NOT NULL, file_id INT UNSIGNED NOT NULL, collection_id INT UNSIGNED NOT NULL, storage_id INT UNSIGNED NOT NULL, filter VARCHAR(32) NOT NULL, size INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_1084B87D93CB796C (file_id), INDEX IDX_1084B87D514956FD (collection_id), INDEX IDX_1084B87D5CC5DB90 (storage_id), UNIQUE INDEX UNIQ_1084B87D7FC45F1D93CB796C (filter, file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_storages (id INT UNSIGNED AUTO_INCREMENT NOT NULL, provider VARCHAR(255) NOT NULL, relative_path VARCHAR(255) NOT NULL, params LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menus (id INT UNSIGNED AUTO_INCREMENT NOT NULL, site_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, properties LONGTEXT DEFAULT NULL, name VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, position SMALLINT DEFAULT 0, INDEX IDX_727508CFF6BD1646 (site_id), INDEX IDX_727508CFA76ED395 (user_id), UNIQUE INDEX name_in_site (name, site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_items (id INT UNSIGNED AUTO_INCREMENT NOT NULL, pid INT UNSIGNED DEFAULT NULL, menu_id INT UNSIGNED DEFAULT NULL, folder_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, open_in_new_window TINYINT(1) DEFAULT \'0\', properties LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'1\', description LONGTEXT DEFAULT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, INDEX IDX_70B2CA2A5550C4ED (pid), INDEX IDX_70B2CA2ACCD7E912 (menu_id), INDEX IDX_70B2CA2A162CB942 (folder_id), INDEX IDX_70B2CA2AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery_albums (id INT UNSIGNED AUTO_INCREMENT NOT NULL, gallery_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, cover_image_id INT DEFAULT NULL, last_image_id INT DEFAULT NULL, photos_count INT NOT NULL, is_enabled TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5661ABED4E7AF8F (gallery_id), INDEX IDX_5661ABEDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galleries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, media_collection_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, order_albums_by INT NOT NULL, created_at DATETIME NOT NULL, title VARCHAR(255) DEFAULT NULL, INDEX IDX_F70E6EB7B52E685C (media_collection_id), INDEX IDX_F70E6EB7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery_photos (id INT UNSIGNED AUTO_INCREMENT NOT NULL, album_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, image_id INT NOT NULL, created_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, position SMALLINT DEFAULT 0, INDEX IDX_AAF50C7B1137ABCF (album_id), INDEX IDX_AAF50C7BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slides (id INT UNSIGNED AUTO_INCREMENT NOT NULL, folder_id INT UNSIGNED DEFAULT NULL, slider_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, file_name VARCHAR(64) NOT NULL, original_file_name VARCHAR(255) NOT NULL, properties LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', uri VARCHAR(255) DEFAULT NULL, is_enabled TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B8C02091D7DF1668 (file_name), INDEX IDX_B8C02091162CB942 (folder_id), INDEX IDX_B8C020912CCC9638 (slider_id), INDEX IDX_B8C02091462CE4F5 (position), INDEX IDX_B8C02091A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sliders (id INT UNSIGNED AUTO_INCREMENT NOT NULL, width SMALLINT DEFAULT NULL, height SMALLINT DEFAULT NULL, mode VARCHAR(10) DEFAULT NULL, library VARCHAR(32) DEFAULT NULL, pause_time INT NOT NULL, web_path VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE webforms_messages (id INT UNSIGNED AUTO_INCREMENT NOT NULL, web_form_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', comment LONGTEXT DEFAULT NULL, status SMALLINT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, ip_address VARCHAR(255) DEFAULT NULL, INDEX IDX_24719905B75935E3 (web_form_id), INDEX IDX_24719905A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE webforms (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, is_ajax TINYINT(1) DEFAULT \'0\' NOT NULL, is_use_captcha TINYINT(1) DEFAULT \'0\' NOT NULL, send_button_title VARCHAR(255) DEFAULT NULL, send_notice_emails VARCHAR(255) DEFAULT NULL, from_email VARCHAR(255) DEFAULT NULL, from_name VARCHAR(255) DEFAULT NULL, final_text LONGTEXT DEFAULT NULL, last_message_date DATETIME DEFAULT NULL, smtp_server VARCHAR(64) DEFAULT NULL, smtp_user VARCHAR(64) DEFAULT NULL, smtp_password VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_641866195E237E06 (name), INDEX IDX_64186619A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE webforms_fields (id INT UNSIGNED AUTO_INCREMENT NOT NULL, web_form_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, is_required TINYINT(1) DEFAULT \'0\' NOT NULL, is_antispam TINYINT(1) DEFAULT \'0\' NOT NULL, name VARCHAR(255) NOT NULL, params LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', params_yaml LONGTEXT DEFAULT NULL, type VARCHAR(12) NOT NULL, is_enabled TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, INDEX IDX_4FE98D46B75935E3 (web_form_id), INDEX IDX_4FE98D46A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unicat__attributes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, configuration_id INT UNSIGNED DEFAULT NULL, items_type_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(32) NOT NULL, search_form_title VARCHAR(32) DEFAULT NULL, search_form_type VARCHAR(32) DEFAULT NULL, open_tag VARCHAR(255) DEFAULT \'<p>\', close_tag VARCHAR(255) DEFAULT \'</p>\', is_dedicated_table TINYINT(1) DEFAULT \'0\' NOT NULL, is_link TINYINT(1) DEFAULT \'0\' NOT NULL, is_required TINYINT(1) DEFAULT \'0\' NOT NULL, is_show_title TINYINT(1) DEFAULT \'1\' NOT NULL, is_primary TINYINT(1) DEFAULT \'1\' NOT NULL, show_in_admin TINYINT(1) NOT NULL, show_in_list TINYINT(1) NOT NULL, show_in_view TINYINT(1) NOT NULL, params LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', params_yaml LONGTEXT DEFAULT NULL, is_items_type_many2many TINYINT(1) DEFAULT \'0\' NOT NULL, is_enabled TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, INDEX IDX_D3165B6D73F32DD8 (configuration_id), INDEX IDX_D3165B6DB9CCD492 (items_type_id), INDEX IDX_D3165B6DA76ED395 (user_id), INDEX IDX_D3165B6D46C53D4C (is_enabled), INDEX IDX_D3165B6DFB9FF2E7 (show_in_admin), INDEX IDX_D3165B6D921EA9F (show_in_list), INDEX IDX_D3165B6DB314B909 (show_in_view), INDEX IDX_D3165B6D462CE4F5 (position), UNIQUE INDEX UNIQ_D3165B6D5E237E0673F32DD8 (name, configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unicat__attributes_groups_relations (attribute_id INT UNSIGNED NOT NULL, group_id INT UNSIGNED NOT NULL, INDEX IDX_E6224AAFB6E62EFA (attribute_id), INDEX IDX_E6224AAFFE54D947 (group_id), PRIMARY KEY(attribute_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unicat__attributes_groups (id INT UNSIGNED AUTO_INCREMENT NOT NULL, configuration_id INT UNSIGNED DEFAULT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, INDEX IDX_5E377FB773F32DD8 (configuration_id), INDEX IDX_5E377FB7462CE4F5 (position), UNIQUE INDEX UNIQ_5E377FB75E237E0673F32DD8 (name, configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unicat__configurations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, media_collection_id INT UNSIGNED DEFAULT NULL, default_taxonomy_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, entities_namespace VARCHAR(255) DEFAULT NULL, is_inheritance TINYINT(1) DEFAULT \'1\' NOT NULL, items_per_page SMALLINT UNSIGNED DEFAULT 10 NOT NULL, icon VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F622D4625E237E06 (name), INDEX IDX_F622D462B52E685C (media_collection_id), INDEX IDX_F622D46241A4F540 (default_taxonomy_id), INDEX IDX_F622D462A76ED395 (user_id), UNIQUE INDEX UNIQ_F622D4622B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unicat__items_types (id INT UNSIGNED AUTO_INCREMENT NOT NULL, configuration_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, content_min_width INT DEFAULT NULL, order_by_attr VARCHAR(255) DEFAULT NULL, order_by_direction VARCHAR(255) DEFAULT NULL, to_string_pattern VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, INDEX IDX_3E08A29773F32DD8 (configuration_id), INDEX IDX_3E08A297A76ED395 (user_id), INDEX IDX_3E08A2975E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unicat__items_types_attributes_groups_relations (attribute_id INT UNSIGNED NOT NULL, group_id INT UNSIGNED NOT NULL, INDEX IDX_927D1F27B6E62EFA (attribute_id), INDEX IDX_927D1F27FE54D947 (group_id), PRIMARY KEY(attribute_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unicat__items_types_taxonomies_relations (attribute_id INT UNSIGNED NOT NULL, taxonomy_id INT UNSIGNED NOT NULL, INDEX IDX_FC6EEED5B6E62EFA (attribute_id), INDEX IDX_FC6EEED59557E6F6 (taxonomy_id), PRIMARY KEY(attribute_id, taxonomy_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unicat__taxonomies (id INT UNSIGNED AUTO_INCREMENT NOT NULL, configuration_id INT UNSIGNED DEFAULT NULL, user_id INT DEFAULT NULL, title_form VARCHAR(255) NOT NULL, is_multiple_entries TINYINT(1) NOT NULL, is_show_in_admin TINYINT(1) DEFAULT \'0\' NOT NULL, is_required TINYINT(1) DEFAULT NULL, is_default_inheritance TINYINT(1) DEFAULT \'0\' NOT NULL, is_tree TINYINT(1) DEFAULT \'1\' NOT NULL, properties LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, INDEX IDX_C1A645E473F32DD8 (configuration_id), INDEX IDX_C1A645E4A76ED395 (user_id), INDEX IDX_C1A645E45E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE yandex_metrika_counters (id INT UNSIGNED AUTO_INCREMENT NOT NULL, token_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, counter_id VARCHAR(255) NOT NULL, is_enabled TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_7141FA06FCEEF2E3 (counter_id), INDEX IDX_7141FA0641DEE7B9 (token_id), INDEX IDX_7141FA06462CE4F5 (position), INDEX IDX_7141FA06A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE yandex_tokens (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, is_enabled TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, position SMALLINT DEFAULT 0, title VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FC086E005F37A13B (token), INDEX IDX_FC086E00462CE4F5 (position), INDEX IDX_FC086E00A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media_categories ADD CONSTRAINT FK_30D688FC727ACA70 FOREIGN KEY (parent_id) REFERENCES media_categories (id)');
        $this->addSql('ALTER TABLE media_collections ADD CONSTRAINT FK_244DA17D14E68FF3 FOREIGN KEY (default_storage_id) REFERENCES media_storages (id)');
        $this->addSql('ALTER TABLE media_files ADD CONSTRAINT FK_192C84E8514956FD FOREIGN KEY (collection_id) REFERENCES media_collections (id)');
        $this->addSql('ALTER TABLE media_files ADD CONSTRAINT FK_192C84E812469DE2 FOREIGN KEY (category_id) REFERENCES media_categories (id)');
        $this->addSql('ALTER TABLE media_files ADD CONSTRAINT FK_192C84E85CC5DB90 FOREIGN KEY (storage_id) REFERENCES media_storages (id)');
        $this->addSql('ALTER TABLE media_files ADD CONSTRAINT FK_192C84E8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE media_files_transformed ADD CONSTRAINT FK_1084B87D93CB796C FOREIGN KEY (file_id) REFERENCES media_files (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_files_transformed ADD CONSTRAINT FK_1084B87D514956FD FOREIGN KEY (collection_id) REFERENCES media_collections (id)');
        $this->addSql('ALTER TABLE media_files_transformed ADD CONSTRAINT FK_1084B87D5CC5DB90 FOREIGN KEY (storage_id) REFERENCES media_storages (id)');
        $this->addSql('ALTER TABLE menus ADD CONSTRAINT FK_727508CFF6BD1646 FOREIGN KEY (site_id) REFERENCES cms_sites (id)');
        $this->addSql('ALTER TABLE menus ADD CONSTRAINT FK_727508CFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE menu_items ADD CONSTRAINT FK_70B2CA2A5550C4ED FOREIGN KEY (pid) REFERENCES menu_items (id)');
        $this->addSql('ALTER TABLE menu_items ADD CONSTRAINT FK_70B2CA2ACCD7E912 FOREIGN KEY (menu_id) REFERENCES menus (id)');
        $this->addSql('ALTER TABLE menu_items ADD CONSTRAINT FK_70B2CA2A162CB942 FOREIGN KEY (folder_id) REFERENCES cms_folders (id)');
        $this->addSql('ALTER TABLE menu_items ADD CONSTRAINT FK_70B2CA2AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE gallery_albums ADD CONSTRAINT FK_5661ABED4E7AF8F FOREIGN KEY (gallery_id) REFERENCES galleries (id)');
        $this->addSql('ALTER TABLE gallery_albums ADD CONSTRAINT FK_5661ABEDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE galleries ADD CONSTRAINT FK_F70E6EB7B52E685C FOREIGN KEY (media_collection_id) REFERENCES media_collections (id)');
        $this->addSql('ALTER TABLE galleries ADD CONSTRAINT FK_F70E6EB7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE gallery_photos ADD CONSTRAINT FK_AAF50C7B1137ABCF FOREIGN KEY (album_id) REFERENCES gallery_albums (id)');
        $this->addSql('ALTER TABLE gallery_photos ADD CONSTRAINT FK_AAF50C7BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE slides ADD CONSTRAINT FK_B8C02091162CB942 FOREIGN KEY (folder_id) REFERENCES cms_folders (id)');
        $this->addSql('ALTER TABLE slides ADD CONSTRAINT FK_B8C020912CCC9638 FOREIGN KEY (slider_id) REFERENCES sliders (id)');
        $this->addSql('ALTER TABLE slides ADD CONSTRAINT FK_B8C02091A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE webforms_messages ADD CONSTRAINT FK_24719905B75935E3 FOREIGN KEY (web_form_id) REFERENCES webforms (id)');
        $this->addSql('ALTER TABLE webforms_messages ADD CONSTRAINT FK_24719905A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE webforms ADD CONSTRAINT FK_64186619A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE webforms_fields ADD CONSTRAINT FK_4FE98D46B75935E3 FOREIGN KEY (web_form_id) REFERENCES webforms (id)');
        $this->addSql('ALTER TABLE webforms_fields ADD CONSTRAINT FK_4FE98D46A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE unicat__attributes ADD CONSTRAINT FK_D3165B6D73F32DD8 FOREIGN KEY (configuration_id) REFERENCES unicat__configurations (id)');
        $this->addSql('ALTER TABLE unicat__attributes ADD CONSTRAINT FK_D3165B6DB9CCD492 FOREIGN KEY (items_type_id) REFERENCES unicat__items_types (id)');
        $this->addSql('ALTER TABLE unicat__attributes ADD CONSTRAINT FK_D3165B6DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE unicat__attributes_groups_relations ADD CONSTRAINT FK_E6224AAFB6E62EFA FOREIGN KEY (attribute_id) REFERENCES unicat__attributes (id)');
        $this->addSql('ALTER TABLE unicat__attributes_groups_relations ADD CONSTRAINT FK_E6224AAFFE54D947 FOREIGN KEY (group_id) REFERENCES unicat__attributes_groups (id)');
        $this->addSql('ALTER TABLE unicat__attributes_groups ADD CONSTRAINT FK_5E377FB773F32DD8 FOREIGN KEY (configuration_id) REFERENCES unicat__configurations (id)');
        $this->addSql('ALTER TABLE unicat__configurations ADD CONSTRAINT FK_F622D462B52E685C FOREIGN KEY (media_collection_id) REFERENCES media_collections (id)');
        $this->addSql('ALTER TABLE unicat__configurations ADD CONSTRAINT FK_F622D46241A4F540 FOREIGN KEY (default_taxonomy_id) REFERENCES unicat__taxonomies (id)');
        $this->addSql('ALTER TABLE unicat__configurations ADD CONSTRAINT FK_F622D462A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE unicat__items_types ADD CONSTRAINT FK_3E08A29773F32DD8 FOREIGN KEY (configuration_id) REFERENCES unicat__configurations (id)');
        $this->addSql('ALTER TABLE unicat__items_types ADD CONSTRAINT FK_3E08A297A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE unicat__items_types_attributes_groups_relations ADD CONSTRAINT FK_927D1F27B6E62EFA FOREIGN KEY (attribute_id) REFERENCES unicat__items_types (id)');
        $this->addSql('ALTER TABLE unicat__items_types_attributes_groups_relations ADD CONSTRAINT FK_927D1F27FE54D947 FOREIGN KEY (group_id) REFERENCES unicat__attributes_groups (id)');
        $this->addSql('ALTER TABLE unicat__items_types_taxonomies_relations ADD CONSTRAINT FK_FC6EEED5B6E62EFA FOREIGN KEY (attribute_id) REFERENCES unicat__items_types (id)');
        $this->addSql('ALTER TABLE unicat__items_types_taxonomies_relations ADD CONSTRAINT FK_FC6EEED59557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES unicat__taxonomies (id)');
        $this->addSql('ALTER TABLE unicat__taxonomies ADD CONSTRAINT FK_C1A645E473F32DD8 FOREIGN KEY (configuration_id) REFERENCES unicat__configurations (id)');
        $this->addSql('ALTER TABLE unicat__taxonomies ADD CONSTRAINT FK_C1A645E4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE yandex_metrika_counters ADD CONSTRAINT FK_7141FA0641DEE7B9 FOREIGN KEY (token_id) REFERENCES yandex_tokens (id)');
        $this->addSql('ALTER TABLE yandex_metrika_counters ADD CONSTRAINT FK_7141FA06A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE yandex_tokens ADD CONSTRAINT FK_FC086E00A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE media_categories DROP FOREIGN KEY FK_30D688FC727ACA70');
        $this->addSql('ALTER TABLE media_files DROP FOREIGN KEY FK_192C84E812469DE2');
        $this->addSql('ALTER TABLE media_files DROP FOREIGN KEY FK_192C84E8514956FD');
        $this->addSql('ALTER TABLE media_files_transformed DROP FOREIGN KEY FK_1084B87D514956FD');
        $this->addSql('ALTER TABLE galleries DROP FOREIGN KEY FK_F70E6EB7B52E685C');
        $this->addSql('ALTER TABLE unicat__configurations DROP FOREIGN KEY FK_F622D462B52E685C');
        $this->addSql('ALTER TABLE media_files_transformed DROP FOREIGN KEY FK_1084B87D93CB796C');
        $this->addSql('ALTER TABLE media_collections DROP FOREIGN KEY FK_244DA17D14E68FF3');
        $this->addSql('ALTER TABLE media_files DROP FOREIGN KEY FK_192C84E85CC5DB90');
        $this->addSql('ALTER TABLE media_files_transformed DROP FOREIGN KEY FK_1084B87D5CC5DB90');
        $this->addSql('ALTER TABLE menu_items DROP FOREIGN KEY FK_70B2CA2ACCD7E912');
        $this->addSql('ALTER TABLE menu_items DROP FOREIGN KEY FK_70B2CA2A5550C4ED');
        $this->addSql('ALTER TABLE gallery_photos DROP FOREIGN KEY FK_AAF50C7B1137ABCF');
        $this->addSql('ALTER TABLE gallery_albums DROP FOREIGN KEY FK_5661ABED4E7AF8F');
        $this->addSql('ALTER TABLE slides DROP FOREIGN KEY FK_B8C020912CCC9638');
        $this->addSql('ALTER TABLE webforms_messages DROP FOREIGN KEY FK_24719905B75935E3');
        $this->addSql('ALTER TABLE webforms_fields DROP FOREIGN KEY FK_4FE98D46B75935E3');
        $this->addSql('ALTER TABLE unicat__attributes_groups_relations DROP FOREIGN KEY FK_E6224AAFB6E62EFA');
        $this->addSql('ALTER TABLE unicat__attributes_groups_relations DROP FOREIGN KEY FK_E6224AAFFE54D947');
        $this->addSql('ALTER TABLE unicat__items_types_attributes_groups_relations DROP FOREIGN KEY FK_927D1F27FE54D947');
        $this->addSql('ALTER TABLE unicat__attributes DROP FOREIGN KEY FK_D3165B6D73F32DD8');
        $this->addSql('ALTER TABLE unicat__attributes_groups DROP FOREIGN KEY FK_5E377FB773F32DD8');
        $this->addSql('ALTER TABLE unicat__items_types DROP FOREIGN KEY FK_3E08A29773F32DD8');
        $this->addSql('ALTER TABLE unicat__taxonomies DROP FOREIGN KEY FK_C1A645E473F32DD8');
        $this->addSql('ALTER TABLE unicat__attributes DROP FOREIGN KEY FK_D3165B6DB9CCD492');
        $this->addSql('ALTER TABLE unicat__items_types_attributes_groups_relations DROP FOREIGN KEY FK_927D1F27B6E62EFA');
        $this->addSql('ALTER TABLE unicat__items_types_taxonomies_relations DROP FOREIGN KEY FK_FC6EEED5B6E62EFA');
        $this->addSql('ALTER TABLE unicat__configurations DROP FOREIGN KEY FK_F622D46241A4F540');
        $this->addSql('ALTER TABLE unicat__items_types_taxonomies_relations DROP FOREIGN KEY FK_FC6EEED59557E6F6');
        $this->addSql('ALTER TABLE yandex_metrika_counters DROP FOREIGN KEY FK_7141FA0641DEE7B9');
        $this->addSql('DROP TABLE media_categories');
        $this->addSql('DROP TABLE media_collections');
        $this->addSql('DROP TABLE media_files');
        $this->addSql('DROP TABLE media_files_transformed');
        $this->addSql('DROP TABLE media_storages');
        $this->addSql('DROP TABLE menus');
        $this->addSql('DROP TABLE menu_items');
        $this->addSql('DROP TABLE gallery_albums');
        $this->addSql('DROP TABLE galleries');
        $this->addSql('DROP TABLE gallery_photos');
        $this->addSql('DROP TABLE slides');
        $this->addSql('DROP TABLE sliders');
        $this->addSql('DROP TABLE webforms_messages');
        $this->addSql('DROP TABLE webforms');
        $this->addSql('DROP TABLE webforms_fields');
        $this->addSql('DROP TABLE unicat__attributes');
        $this->addSql('DROP TABLE unicat__attributes_groups_relations');
        $this->addSql('DROP TABLE unicat__attributes_groups');
        $this->addSql('DROP TABLE unicat__configurations');
        $this->addSql('DROP TABLE unicat__items_types');
        $this->addSql('DROP TABLE unicat__items_types_attributes_groups_relations');
        $this->addSql('DROP TABLE unicat__items_types_taxonomies_relations');
        $this->addSql('DROP TABLE unicat__taxonomies');
        $this->addSql('DROP TABLE yandex_metrika_counters');
        $this->addSql('DROP TABLE yandex_tokens');
    }
}
