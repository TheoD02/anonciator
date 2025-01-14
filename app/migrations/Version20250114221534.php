<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114221534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation (id SERIAL NOT NULL, announce_id INT DEFAULT NULL, initialized_by_id INT NOT NULL, receiver_id INT NOT NULL, name VARCHAR(200) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8A8E26E96F5DA3DE ON conversation (announce_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E95B3F0E5D ON conversation (initialized_by_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9CD53EDB6 ON conversation (receiver_id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E96F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E95B3F0E5D FOREIGN KEY (initialized_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD conversation_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
        $this->addSql('ALTER TABLE "user" ADD password VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E96F5DA3DE');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E95B3F0E5D');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E9CD53EDB6');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP INDEX IDX_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE message DROP conversation_id');
        $this->addSql('ALTER TABLE "user" DROP password');
    }
}
