<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202174104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $comments = [
            "Super figure, impressionnant à voir en compétition !",
            "J'adore cette technique, mais elle demande beaucoup d'équilibre.",
            "Je l'ai essayée et je me suis crashé... Respect à ceux qui la maîtrisent !",
            "Un classique du freestyle, mais toujours impressionnant.",
            "Ça demande une sacrée dose de courage et de technique.",
            "Je rêve de pouvoir réaliser ce trick un jour.",
            "Incroyable, surtout quand c'est bien exécuté avec du style.",
            "Les pros le font paraître facile, mais c'est vraiment technique.",
            "Une des figures les plus stylées à voir en half-pipe.",
            "Cette figure a marqué l'histoire du snowboard.",
            "Un trick incontournable pour tout rider avancé.",
            "J'ai essayé sur un kicker, c'est plus difficile que ça en a l'air !",
            "Une belle rotation bien maîtrisée, c'est vraiment beau à voir.",
            "Quand c'est bien plaqué, ça donne une sensation incroyable.",
            "J'adore voir cette figure réalisée avec un bon grab."
        ];

        foreach ($comments as $comment) {
            $escapedComment = addslashes($comment);
            $this->addSql("INSERT INTO comments (tricks_id, author_id, content, created_at) 
                SELECT t.id, 1, '$escapedComment', NOW() FROM tricks t");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM comments WHERE author_id = 1");
    }
}
