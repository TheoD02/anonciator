<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103050601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307fb1cce068');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f2b1e8a09');
        $this->addSql('DROP INDEX idx_b6bd307f2b1e8a09');
        $this->addSql('DROP INDEX idx_b6bd307fb1cce068');
        $this->addSql('ALTER TABLE message ADD sent_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD sent_to_id INT NOT NULL');
        $this->addSql('ALTER TABLE message DROP sended_by_id');
        $this->addSql('ALTER TABLE message DROP sended_to_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA45BB98C FOREIGN KEY (sent_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3E89D3ED FOREIGN KEY (sent_to_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B6BD307FA45BB98C ON message (sent_by_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F3E89D3ED ON message (sent_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FA45BB98C');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F3E89D3ED');
        $this->addSql('DROP INDEX IDX_B6BD307FA45BB98C');
        $this->addSql('DROP INDEX IDX_B6BD307F3E89D3ED');
        $this->addSql('ALTER TABLE message ADD sended_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD sended_to_id INT NOT NULL');
        $this->addSql('ALTER TABLE message DROP sent_by_id');
        $this->addSql('ALTER TABLE message DROP sent_to_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307fb1cce068 FOREIGN KEY (sended_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f2b1e8a09 FOREIGN KEY (sended_to_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b6bd307f2b1e8a09 ON message (sended_to_id)');
        $this->addSql('CREATE INDEX idx_b6bd307fb1cce068 ON message (sended_by_id)');
    }
}
