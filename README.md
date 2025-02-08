# Blog OpenClassrooms

![banner](https://github.com/user-attachments/assets/000bdfe6-a392-4a96-8ea5-7395207a34c7)

Projet de la formation ***Développeur d'application - PHP / Symfony***.

**Développez de A à Z le site communautaire SnowTricks** - [Lien de la formation](https://openclassrooms.com/fr/paths/876-developpeur-dapplication-php-symfony)

## Contexte

Jimmy Sweat est un entrepreneur ambitieux passionné de snowboard. Son objectif est la création d'un site collaboratif pour faire connaître ce sport auprès du grand public et aider à l'apprentissage des figures (tricks).
Il souhaite capitaliser sur du contenu apporté par les internautes afin de développer un contenu riche et suscitant l’intérêt des utilisateurs du site. Par la suite, Jimmy souhaite développer un business de mise en relation avec les marques de snowboard grâce au trafic que le contenu aura généré.
Pour ce projet, nous allons nous concentrer sur la création technique du site pour Jimmy.

## Descriptif du besoin 

Vous êtes chargé de développer le site répondant aux besoins de Jimmy. Vous devez ainsi implémenter les fonctionnalités suivantes : 
 
*   un annuaire des figures de snowboard. Vous pouvez vous inspirer de la liste des figures sur Wikipédia. Contentez-vous d'intégrer 10 figures, le reste sera saisi par les internautes 
*   la gestion des figures (création, modification, consultation)
*   un espace de discussion commun à toutes les figures

Pour implémenter ces fonctionnalités, vous devez créer les pages suivantes :
 
*   la page d’accueil où figurera la liste des figures
*   la page de création d'une nouvelle figure
*   la page de modification d'une figure
*   la page de présentation d’une figure (contenant l’espace de discussion commun autour d’une figure)
 
Nota bene

Il faut que les URL de page permettent une compréhension rapide de ce que la page représente et que le référencement naturel soit facilité.
L’utilisation de bundles tiers est interdite sauf pour les données initiales. Vous utiliserez les compétences acquises jusqu’ici ainsi que la documentation officielle afin de remplir les objectifs donnés.
Le design du site web est laissé complètement libre, attention cependant à respecter les wireframes fournis pour le gabarit de vos pages. Néanmoins, il faudra que le site soit consultable aussi bien sur un ordinateur que sur mobile (téléphone mobile, tablette, phablette…).
En premier lieu, il vous faudra écrire l’ensemble des issues/tickets afin de découper votre travail méthodiquement et de vous assurer que l’ensemble du besoin client soit bien compris avec votre mentor. Les tickets/issues seront écrits dans un repository GitHub que vous aurez créé au préalable.
L’ensemble des figures de snowboard doivent être présentes à l’initialisation de l’application web. Vous utiliserez un bundle externe pour charger ces données. 


## Installation du projet 

*   Cloner le projet

*   Renseigner les informations requises dans le .env (bdd, mail...)

*   Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
```

*   Lancer les migrations pour mettre à jour la base de données

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

*   Créer un build de l'application

```bash
npm run build
```

*   Un compte vérifié est créé automatiquement et vous avez la possibilité de créer un compte utilisateur simple
    - username : demo
    - password : testDemo


## Auteur

**Jonathan Dumont** - *OC-P5-Blog php* - [Joz](https://github.com/JozBLT)
