<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114234913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f6f5da3de');
        $this->addSql('DROP INDEX idx_b6bd307f6f5da3de');
        $this->addSql('ALTER TABLE message DROP announce_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message ADD announce_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f6f5da3de FOREIGN KEY (announce_id) REFERENCES announce (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b6bd307f6f5da3de ON message (announce_id)');
    }
}
