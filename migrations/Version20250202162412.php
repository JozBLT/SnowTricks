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
        $this->addSql("INSERT INTO tricks (title, content, slug, category_id, created_by_id, created_at, updated_at) VALUES 
            ('Backflip', 'Le backflip est un saut périlleux arrière où le snowboardeur effectue une rotation complète en arrière tout en restant aligné avec la direction du saut. Cette figure nécessite une maîtrise parfaite du timing et de l\'équilibre pour assurer une rotation fluide et un atterrissage en douceur.', 'backflip', 1, 1, NOW(), NOW()),
            ('Frontflip', 'Le frontflip est l\'inverse du backflip, impliquant une rotation avant complète. Le snowboardeur projette son corps vers l\'avant, réalisant un saut périlleux avant. Cette figure demande une impulsion puissante et une coordination précise pour réussir la rotation et l\'atterrissage.', 'frontflip', 1, 1, NOW(), NOW()),
            ('360', 'Le 360 consiste en une rotation horizontale de 360 degrés, soit un tour complet, pendant un saut. Le snowboardeur peut effectuer cette rotation en frontside (vers l\'avant) ou en backside (vers l\'arrière), ajoutant une dimension stylistique à la figure.', '360', 1, 1, NOW(), NOW()),
            ('540', 'Le 540 est une rotation de 540 degrés, soit un tour et demi. Cette figure combine la rotation du 360 avec un demi-tour supplémentaire, nécessitant une vitesse de rotation accrue et une anticipation de l\'atterrissage en switch (position inverse).', '540', 1, 1, NOW(), NOW()),
            ('720', 'Le 720 implique deux rotations complètes, soit 720 degrés. Cette figure avancée requiert une excellente maîtrise de la rotation et de l\'équilibre, ainsi qu\'une préparation minutieuse pour assurer un atterrissage stable après deux tours en l\'air.', '720', 1, 1, NOW(), NOW()),
            ('Method Grab', 'Le Method Grab est une figure emblématique du snowboard freestyle. Le rider attrape la carre backside de la planche entre les pieds avec la main avant tout en fléchissant les genoux et en arquant le dos, ce qui donne une apparence stylée et accentue l\'esthétique du saut.', 'method-grab', 1, 1, NOW(), NOW()),
            ('Indy Grab', 'L\'Indy Grab consiste à saisir la carre frontside de la planche entre les pieds avec la main arrière pendant un saut. Cette figure est couramment utilisée dans diverses combinaisons de sauts et ajoute une touche stylistique aux rotations et flips.', 'indy-grab', 1, 1, NOW(), NOW()),
            ('Mute Grab', 'Le Mute Grab implique de saisir la carre frontside de la planche entre les pieds avec la main avant tout en rapprochant les genoux vers la poitrine. Cette figure est appréciée pour son esthétique et est souvent intégrée dans des rotations pour ajouter du style.', 'mute-grab', 1, 1, NOW(), NOW()),
            ('McTwist', 'Le McTwist est une figure combinant une rotation horizontale de 540 degrés avec un flip arrière. Souvent réalisée dans un half-pipe, cette figure nécessite une coordination exceptionnelle et une compréhension approfondie de la dynamique du corps en l\'air.', 'mctwist', 1, 1, NOW(), NOW()),
            ('Butter', 'Le Butter est une figure réalisée sur le plat, où le snowboardeur transfère son poids sur le nose ou le tail de la planche tout en effectuant des rotations ou des mouvements fluides. Cette figure demande un excellent équilibre et une compréhension fine du contrôle de la planche.', 'butter', 2, 1, NOW(), NOW()),
            ('Boardslide', 'Le Boardslide est une figure où le snowboardeur glisse perpendiculairement sur un rail ou une box, avec la planche positionnée à 90 degrés par rapport à l\'obstacle.', 'boardslide', 3, 1, NOW(), NOW()),
            ('50-50', 'Le 50-50 est l\'une des figures de rail les plus fondamentales en snowboard. Il consiste à glisser en ligne droite sur un rail ou une box, avec la planche parfaitement alignée. Cette figure est souvent la première que les riders apprennent dans le jibbing, car elle permet de comprendre le contrôle du slide et la gestion du poids sur un module.', '50-50', 3, 1, NOW(), NOW()),
            ('Lipslide', 'Le Lipslide est une variation du Boardslide qui ajoute une difficulté supplémentaire. Le snowboardeur entre sur le rail en passant d\'abord par le tail de la planche, ce qui requiert un bon contrôle du pop et de la rotation avant d\'atterrir sur l\'obstacle. Cette figure demande un excellent équilibre et une sortie contrôlée.', 'lipslide', 3, 1, NOW(), NOW()),
            ('Tail Press', 'Le Tail Press est une figure où le snowboardeur transfère son poids sur l\'arrière de la planche, en soulevant légèrement l\'avant tout en glissant sur une surface plane ou un rail. C\'est une figure de style qui demande de la précision et un bon contrôle du poids du corps pour maintenir l\'équilibre sans que la planche touche trop la neige.', 'tail-press', 2, 1, NOW(), NOW()),
            ('Nose Press', 'Le Nose Press est similaire au Tail Press, mais cette fois, le rider transfère son poids sur l\'avant de la planche, soulevant le tail tout en glissant. Cette figure exige une grande stabilité et une gestion précise du centre de gravité pour éviter de tomber en avant.', 'nose-press', 2, 1, NOW(), NOW()),
            ('Stalefish', 'Le Stalefish est un grab qui consiste à saisir la carre backside de la planche avec la main arrière, généralement entre les fixations. Cette figure est célèbre pour son style unique et est souvent combinée avec des rotations aériennes. L\'ajout d\'une flexion marquée des jambes permet d\'accentuer l\'esthétique de la figure.', 'stalefish', 1, 1, NOW(), NOW()),
            ('Japan Air', 'Le Japan Air est un grab iconique où le snowboardeur saisit la carre frontside entre les fixations avec la main avant tout en fléchissant fortement la jambe arrière et en ramenant la planche vers l\'arrière. Cette figure est très appréciée pour son style distinctif et est souvent exécutée en half-pipe ou sur de gros sauts.', 'japan-air', 1, 1, NOW(), NOW()),
            ('Misty Flip', 'Le Misty Flip est un flip avant combiné avec une rotation horizontale de 540 degrés. Contrairement aux simples rotations, ce trick implique une dynamique de rotation inversée qui demande une synchronisation parfaite entre l\'impulsion et l\'atterrissage.', 'misty-flip', 1, 1, NOW(), NOW())");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM tricks WHERE title IN (
            'Backflip', 'Frontflip', '360', '540', '720', 'Method Grab', 'Indy Grab', 'Mute Grab',
            'McTwist', 'Butter', 'Boardslide', '50-50', 'Lipslide', 'Tail Press', 'Nose Press',
            'Stalefish', 'Japan Air', 'Misty Flip'
        )");

    }
}
