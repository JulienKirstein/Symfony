<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191113093639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCBA9CD190');
        $this->addSql('DROP INDEX IDX_67F068BCBA9CD190 ON commentaire');
        $this->addSql('ALTER TABLE commentaire ADD commentaire VARCHAR(255) NOT NULL, DROP commentaire_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commentaire ADD commentaire_id INT DEFAULT NULL, DROP commentaire');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCBA9CD190 FOREIGN KEY (commentaire_id) REFERENCES application (id)');
        $this->addSql('CREATE INDEX IDX_67F068BCBA9CD190 ON commentaire (commentaire_id)');
    }
}
