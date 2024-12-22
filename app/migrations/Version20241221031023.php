<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221031023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announce (id SERIAL NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, location NUMERIC(10, 8) NOT NULL, status VARCHAR(40) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E6D6DD7512469DE2 ON announce (category_id)');
        $this->addSql('CREATE TABLE announce_category (id SERIAL NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE group_role (id SERIAL NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE group_role_role (group_role_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(group_role_id, role_id))');
        $this->addSql('CREATE INDEX IDX_2965A41F500376A0 ON group_role_role (group_role_id)');
        $this->addSql('CREATE INDEX IDX_2965A41FD60322AC ON group_role_role (role_id)');
        $this->addSql('CREATE TABLE message (id SERIAL NOT NULL, sended_by_id INT NOT NULL, sended_to_id INT NOT NULL, content TEXT NOT NULL, was_read_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307FB1CCE068 ON message (sended_by_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F2B1E8A09 ON message (sended_to_id)');
        $this->addSql('COMMENT ON COLUMN message.was_read_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE resource (id SERIAL NOT NULL, path VARCHAR(255) NOT NULL, bucket VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE role (id SERIAL NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, username VARCHAR(25) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_group_role (user_id INT NOT NULL, group_role_id INT NOT NULL, PRIMARY KEY(user_id, group_role_id))');
        $this->addSql('CREATE INDEX IDX_D95417F6A76ED395 ON user_group_role (user_id)');
        $this->addSql('CREATE INDEX IDX_D95417F6500376A0 ON user_group_role (group_role_id)');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD7512469DE2 FOREIGN KEY (category_id) REFERENCES announce_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_role_role ADD CONSTRAINT FK_2965A41F500376A0 FOREIGN KEY (group_role_id) REFERENCES group_role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_role_role ADD CONSTRAINT FK_2965A41FD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FB1CCE068 FOREIGN KEY (sended_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2B1E8A09 FOREIGN KEY (sended_to_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_group_role ADD CONSTRAINT FK_D95417F6A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_group_role ADD CONSTRAINT FK_D95417F6500376A0 FOREIGN KEY (group_role_id) REFERENCES group_role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE announce DROP CONSTRAINT FK_E6D6DD7512469DE2');
        $this->addSql('ALTER TABLE group_role_role DROP CONSTRAINT FK_2965A41F500376A0');
        $this->addSql('ALTER TABLE group_role_role DROP CONSTRAINT FK_2965A41FD60322AC');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FB1CCE068');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F2B1E8A09');
        $this->addSql('ALTER TABLE user_group_role DROP CONSTRAINT FK_D95417F6A76ED395');
        $this->addSql('ALTER TABLE user_group_role DROP CONSTRAINT FK_D95417F6500376A0');
        $this->addSql('DROP TABLE announce');
        $this->addSql('DROP TABLE announce_category');
        $this->addSql('DROP TABLE group_role');
        $this->addSql('DROP TABLE group_role_role');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE resource');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_group_role');
    }
}
