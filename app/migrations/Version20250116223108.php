<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116223108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announce (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, location NUMERIC(10, 8) NOT NULL, status VARCHAR(40) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E6D6DD7512469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE announce_photos (announce_id INT NOT NULL, resource_id INT NOT NULL, INDEX IDX_1414633A6F5DA3DE (announce_id), INDEX IDX_1414633A89329D25 (resource_id), PRIMARY KEY(announce_id, resource_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE announce_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, announce_id INT DEFAULT NULL, initialized_by_id INT NOT NULL, receiver_id INT NOT NULL, name VARCHAR(200) NOT NULL, INDEX IDX_8A8E26E96F5DA3DE (announce_id), INDEX IDX_8A8E26E95B3F0E5D (initialized_by_id), INDEX IDX_8A8E26E9CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_role_role (group_role_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2965A41F500376A0 (group_role_id), INDEX IDX_2965A41FD60322AC (role_id), PRIMARY KEY(group_role_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, conversation_id INT NOT NULL, content LONGTEXT NOT NULL, sent_by VARCHAR(255) NOT NULL, sent_to VARCHAR(255) NOT NULL, was_read_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B6BD307F9AC0396 (conversation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, bucket VARCHAR(50) NOT NULL, original_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group_role (user_id INT NOT NULL, group_role_id INT NOT NULL, INDEX IDX_D95417F6A76ED395 (user_id), INDEX IDX_D95417F6500376A0 (group_role_id), PRIMARY KEY(user_id, group_role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD7512469DE2 FOREIGN KEY (category_id) REFERENCES announce_category (id)');
        $this->addSql('ALTER TABLE announce_photos ADD CONSTRAINT FK_1414633A6F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE announce_photos ADD CONSTRAINT FK_1414633A89329D25 FOREIGN KEY (resource_id) REFERENCES resource (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E96F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E95B3F0E5D FOREIGN KEY (initialized_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE group_role_role ADD CONSTRAINT FK_2965A41F500376A0 FOREIGN KEY (group_role_id) REFERENCES group_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_role_role ADD CONSTRAINT FK_2965A41FD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE user_group_role ADD CONSTRAINT FK_D95417F6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_role ADD CONSTRAINT FK_D95417F6500376A0 FOREIGN KEY (group_role_id) REFERENCES group_role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY FK_E6D6DD7512469DE2');
        $this->addSql('ALTER TABLE announce_photos DROP FOREIGN KEY FK_1414633A6F5DA3DE');
        $this->addSql('ALTER TABLE announce_photos DROP FOREIGN KEY FK_1414633A89329D25');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E96F5DA3DE');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E95B3F0E5D');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9CD53EDB6');
        $this->addSql('ALTER TABLE group_role_role DROP FOREIGN KEY FK_2965A41F500376A0');
        $this->addSql('ALTER TABLE group_role_role DROP FOREIGN KEY FK_2965A41FD60322AC');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE user_group_role DROP FOREIGN KEY FK_D95417F6A76ED395');
        $this->addSql('ALTER TABLE user_group_role DROP FOREIGN KEY FK_D95417F6500376A0');
        $this->addSql('DROP TABLE announce');
        $this->addSql('DROP TABLE announce_photos');
        $this->addSql('DROP TABLE announce_category');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE group_role');
        $this->addSql('DROP TABLE group_role_role');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE resource');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_group_role');
    }
}
