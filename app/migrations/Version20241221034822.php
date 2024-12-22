<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221034822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announce_photos (announce_id INT NOT NULL, resource_id INT NOT NULL, PRIMARY KEY(announce_id, resource_id))');
        $this->addSql('CREATE INDEX IDX_1414633A6F5DA3DE ON announce_photos (announce_id)');
        $this->addSql('CREATE INDEX IDX_1414633A89329D25 ON announce_photos (resource_id)');
        $this->addSql('ALTER TABLE announce_photos ADD CONSTRAINT FK_1414633A6F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE announce_photos ADD CONSTRAINT FK_1414633A89329D25 FOREIGN KEY (resource_id) REFERENCES resource (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE announce_photos DROP CONSTRAINT FK_1414633A6F5DA3DE');
        $this->addSql('ALTER TABLE announce_photos DROP CONSTRAINT FK_1414633A89329D25');
        $this->addSql('DROP TABLE announce_photos');
    }
}
