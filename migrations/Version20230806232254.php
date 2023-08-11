<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230806232254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat_detail ADD relation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE achat_detail ADD CONSTRAINT FK_EA7F1F563256915B FOREIGN KEY (relation_id) REFERENCES historique_commission (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EA7F1F563256915B ON achat_detail (relation_id)');
        $this->addSql('ALTER TABLE historique_commission DROP FOREIGN KEY FK_6431BBB13256915B');
        $this->addSql('DROP INDEX UNIQ_6431BBB13256915B ON historique_commission');
        $this->addSql('ALTER TABLE historique_commission DROP relation_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat_detail DROP FOREIGN KEY FK_EA7F1F563256915B');
        $this->addSql('DROP INDEX UNIQ_EA7F1F563256915B ON achat_detail');
        $this->addSql('ALTER TABLE achat_detail DROP relation_id');
        $this->addSql('ALTER TABLE historique_commission ADD relation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE historique_commission ADD CONSTRAINT FK_6431BBB13256915B FOREIGN KEY (relation_id) REFERENCES achat_detail (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6431BBB13256915B ON historique_commission (relation_id)');
    }
}
