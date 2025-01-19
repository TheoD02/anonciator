<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250119144010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E96F5DA3DE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E96F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E96F5DA3DE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E96F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id)');
    }
}
