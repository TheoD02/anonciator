<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102223947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce ADD created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE announce ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE announce ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE announce ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE message ADD announce_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE message ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B6BD307F6F5DA3DE ON message (announce_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F6F5DA3DE');
        $this->addSql('DROP INDEX IDX_B6BD307F6F5DA3DE');
        $this->addSql('ALTER TABLE message DROP announce_id');
        $this->addSql('ALTER TABLE message DROP created_at');
        $this->addSql('ALTER TABLE message DROP updated_at');
        $this->addSql('ALTER TABLE announce DROP created_by');
        $this->addSql('ALTER TABLE announce DROP updated_by');
        $this->addSql('ALTER TABLE announce DROP created_at');
        $this->addSql('ALTER TABLE announce DROP updated_at');
    }
}
