<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240827015400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add an admin user to the Utilisateur table';
    }

    public function up(Schema $schema): void
    {

        // Insert roles with permissions
        $this->addSql(
            "INSERT IGNORE INTO role (Nom, Permissions) VALUES 
            ('ROLE_USER', '[\"VIEW_ARTICLES\", \"EDIT_OWN_PROFILE\"]'), 
            ('ROLE_ADMIN', '[\"VIEW_ARTICLES\", \"EDIT_OWN_PROFILE\", \"MANAGE_USERS\", \"VIEW_ADMIN_DASHBOARD\"]')"
        );

        // Fetch role IDs
        $this->addSql("SET @role_user_id := (SELECT id FROM role WHERE Nom = 'ROLE_USER');");
        $this->addSql("SET @role_admin_id := (SELECT id FROM role WHERE Nom = 'ROLE_ADMIN');");
        
        // Insert an admin user
        $this->addSql(
            "INSERT INTO utilisateur (Nom, Email, mot_de_passe, role_id) 
             VALUES ('Admin', 'admin@gmail.com', '" . password_hash('123456', PASSWORD_BCRYPT) . "', @role_admin_id)"
        );
    }

    public function down(Schema $schema): void
    {
        // Remove the admin user
        $this->addSql("DELETE FROM utilisateur WHERE Email = 'admin@gmail.com'");

        // Remove roles
        $this->addSql("DELETE FROM role WHERE Nom IN ('ROLE_USER', 'ROLE_ADMIN')");
    }
}
