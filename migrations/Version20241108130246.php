<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241108130246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tricks ADD created_by_id INT NOT NULL');
        $this->addSql('UPDATE tricks SET created_by_id = 1');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C1B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E1D902C1B03A8386 ON tricks (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tricks DROP FOREIGN KEY FK_E1D902C1B03A8386');
        $this->addSql('DROP INDEX IDX_E1D902C1B03A8386 ON tricks');
        $this->addSql('ALTER TABLE tricks DROP created_by_id');
    }
}
