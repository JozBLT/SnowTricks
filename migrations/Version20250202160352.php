<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202160352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO categories (name, created_at, updated_at) VALUES 
                                                          ('Freestyle', NOW(), NOW()), 
                                                          ('Freeride', NOW(), NOW()), 
                                                          ('Alpin', NOW(), NOW()), 
                                                          ('Boardercross', NOW(), NOW())");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE tricks SET category_id = NULL WHERE category_id IN 
                      (SELECT id FROM categories WHERE name IN ('Freestyle', 'Freeride', 'Alpin', 'Boardercross'))");

        $this->addSql("DELETE FROM categories WHERE name IN ('Freestyle', 'Freeride', 'Alpin', 'Boardercross')");
    }
}
