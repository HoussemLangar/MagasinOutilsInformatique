<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240827002608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial creation of tables and relationships for the application';
    }

    public function up(Schema $schema): void
    {
        // Creating tables
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(255) NOT NULL, quantite INT NOT NULL, niveau_min INT NOT NULL, niveau_max INT NOT NULL, date_entree DATE NOT NULL, emplacement_id INT NOT NULL, INDEX IDX_23A0E66C4598A51 (emplacement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE emplacement (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, magasin_id INT NOT NULL, INDEX IDX_C0CF65F620096AE3 (magasin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE etiquette (id INT AUTO_INCREMENT NOT NULL, contenu LONGBLOB DEFAULT NULL, article_id INT NOT NULL, INDEX IDX_1E0E195A7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE magasin (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE operation (id INT AUTO_INCREMENT NOT NULL, num_operation INT NOT NULL, date_sortie DATE NOT NULL, quantite INT NOT NULL, type VARCHAR(255) NOT NULL, article_id INT NOT NULL, UNIQUE INDEX UNIQ_1981A66D6B5FD2B1 (num_operation), INDEX IDX_1981A66D7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rapport (id INT AUTO_INCREMENT NOT NULL, date_jour DATE NOT NULL, quantite INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rapport_operation (rapport_id INT NOT NULL, operation_id INT NOT NULL, INDEX IDX_1AE384811DFBCC46 (rapport_id), INDEX IDX_1AE3848144AC3583 (operation_id), PRIMARY KEY(rapport_id, operation_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rapport_article (rapport_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_79EA6E051DFBCC46 (rapport_id), INDEX IDX_79EA6E057294869C (article_id), PRIMARY KEY(rapport_id, article_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, permissions JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, role_id INT NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');

        // Adding foreign keys
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66C4598A51 FOREIGN KEY (emplacement_id) REFERENCES emplacement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE emplacement ADD CONSTRAINT FK_C0CF65F620096AE3 FOREIGN KEY (magasin_id) REFERENCES magasin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etiquette ADD CONSTRAINT FK_1E0E195A7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rapport_operation ADD CONSTRAINT FK_1AE384811DFBCC46 FOREIGN KEY (rapport_id) REFERENCES rapport (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rapport_operation ADD CONSTRAINT FK_1AE3848144AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rapport_article ADD CONSTRAINT FK_79EA6E051DFBCC46 FOREIGN KEY (rapport_id) REFERENCES rapport (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rapport_article ADD CONSTRAINT FK_79EA6E057294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Dropping foreign keys
        $this->addSql('ALTER TABLE rapport_operation DROP FOREIGN KEY FK_1AE384811DFBCC46');
        $this->addSql('ALTER TABLE rapport_operation DROP FOREIGN KEY FK_1AE3848144AC3583');
        $this->addSql('ALTER TABLE rapport_article DROP FOREIGN KEY FK_79EA6E051DFBCC46');
        $this->addSql('ALTER TABLE rapport_article DROP FOREIGN KEY FK_79EA6E057294869C');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66C4598A51');
        $this->addSql('ALTER TABLE emplacement DROP FOREIGN KEY FK_C0CF65F620096AE3');
        $this->addSql('ALTER TABLE etiquette DROP FOREIGN KEY FK_1E0E195A7294869C');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D7294869C');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3D60322AC');

        // Dropping tables
        $this->addSql('DROP TABLE rapport_operation');
        $this->addSql('DROP TABLE rapport_article');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE emplacement');
        $this->addSql('DROP TABLE etiquette');
        $this->addSql('DROP TABLE magasin');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP TABLE rapport');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
