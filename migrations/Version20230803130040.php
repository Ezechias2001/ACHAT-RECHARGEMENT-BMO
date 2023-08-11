<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230803130040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achat_detail (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, date_naissance VARCHAR(255) DEFAULT NULL, sexe VARCHAR(1) DEFAULT NULL, piece VARCHAR(255) DEFAULT NULL, numero_piece VARCHAR(255) DEFAULT NULL, image_piece VARCHAR(255) DEFAULT NULL, type_carte VARCHAR(255) DEFAULT NULL, date_creation VARCHAR(255) DEFAULT NULL, montant INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE achat_gros (id INT AUTO_INCREMENT NOT NULL, nom_service VARCHAR(255) DEFAULT NULL, nombre INT DEFAULT NULL, type_carte VARCHAR(255) DEFAULT NULL, montant INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commission (id INT AUTO_INCREMENT NOT NULL, solde INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historique_commission (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, gain_commission INT DEFAULT NULL, type_carte VARCHAR(255) DEFAULT NULL, montant INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rechargement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, numero_carte INT DEFAULT NULL, type_carte VARCHAR(255) DEFAULT NULL, montant INT DEFAULT NULL, date_creation VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE achat_detail');
        $this->addSql('DROP TABLE achat_gros');
        $this->addSql('DROP TABLE commission');
        $this->addSql('DROP TABLE historique_commission');
        $this->addSql('DROP TABLE rechargement');
    }
}
