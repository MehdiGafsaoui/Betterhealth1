<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20250209123614 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // This method contains SQL commands to modify the database schema
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // This method contains SQL to revert the database schema change
        $this->addSql('DROP TABLE users');
    }
}