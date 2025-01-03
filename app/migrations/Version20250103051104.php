<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103051104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307fa45bb98c');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f3e89d3ed');
        $this->addSql('DROP INDEX idx_b6bd307f3e89d3ed');
        $this->addSql('DROP INDEX idx_b6bd307fa45bb98c');
        $this->addSql('ALTER TABLE message ADD sent_by VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE message ADD sent_to VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE message DROP sent_by_id');
        $this->addSql('ALTER TABLE message DROP sent_to_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message ADD sent_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD sent_to_id INT NOT NULL');
        $this->addSql('ALTER TABLE message DROP sent_by');
        $this->addSql('ALTER TABLE message DROP sent_to');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307fa45bb98c FOREIGN KEY (sent_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f3e89d3ed FOREIGN KEY (sent_to_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b6bd307f3e89d3ed ON message (sent_to_id)');
        $this->addSql('CREATE INDEX idx_b6bd307fa45bb98c ON message (sent_by_id)');
    }
}
