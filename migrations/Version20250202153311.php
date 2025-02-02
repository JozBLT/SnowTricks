<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202153311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $hashedPassword = password_hash('testDemo', PASSWORD_BCRYPT);

        $this->addSql("INSERT INTO user (username, email, roles, password, is_verified) VALUES 
            ('SnowTricks', 'fake@mail.fr', '[]', NULL, FALSE), 
            ('demo', 'demo@mail.fr', '[]', '$hashedPassword', TRUE)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM user WHERE username = 'demo'");
    }
}
