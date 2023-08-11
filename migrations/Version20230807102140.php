<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230807102140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rechargement ADD relation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rechargement ADD CONSTRAINT FK_479F0C503256915B FOREIGN KEY (relation_id) REFERENCES historique_commission (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_479F0C503256915B ON rechargement (relation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rechargement DROP FOREIGN KEY FK_479F0C503256915B');
        $this->addSql('DROP INDEX UNIQ_479F0C503256915B ON rechargement');
        $this->addSql('ALTER TABLE rechargement DROP relation_id');
    }
}
