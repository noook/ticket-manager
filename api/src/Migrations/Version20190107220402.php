<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190107220402 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE ticket_user (ticket_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(ticket_id, user_id))');
        $this->addSql('CREATE INDEX IDX_BF48C371700047D2 ON ticket_user (ticket_id)');
        $this->addSql('CREATE INDEX IDX_BF48C371A76ED395 ON ticket_user (user_id)');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket DROP participants');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE ticket_user');
        $this->addSql('ALTER TABLE ticket ADD participants JSON DEFAULT NULL');
    }
}
