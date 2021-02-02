<p align="center"><img width=400px src="https://skript-mc.fr/assets/images/logo.png"></p>
<h2 align="center">Swan Dashboard</h2>
<p align="center">
    Swan Dashboard est un panel de gestion spécialement développé pour <a href="https://github.com/Skript-MC/Swan">Swan</a> via Symfony et MongoDB.
</p>

[![Maintainability](https://api.codeclimate.com/v1/badges/83ba962d1237ac5048c1/maintainability)](https://codeclimate.com/github/Romitou/SwanDashboard/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/83ba962d1237ac5048c1/test_coverage)](https://codeclimate.com/github/Romitou/SwanDashboard/test_coverage) ![GitHub](https://img.shields.io/github/license/Romitou/SwanDashboard)

## 🚀 Installation

- Installez [PHP 7.4+](https://www.php.net/downloads) & [Composer](https://getcomposer.org/) sur votre machine ;
- Installez [MongoDB PHP Driver](https://docs.mongodb.com/drivers/php) via PECL, avec `sudo pecl install mongodb`. N'oubliez pas d'ajouter `extension=mongodb.so` dans votre `php.ini` ;
- Téléchargez la [dernière version stable](https://github.com/Romitou/SwanDashboard/releases/latest) ou clonez ce dépôt pour tester les dernières modifications ;
- Renommez `.env.example` en `.env`, et remplissez les champs comme indiqué par les commentaires dans le fichier ;
- Exécutez la commande `yarn` pour installer les modules JavaScript nécessaires. Si la commande `yarn` n'a pas été trouvée, faites `npm i -g yarn` et recommencez cette étape ;
- Exécutez la commande `composer install` pour installer les modules nécessaires. Si la commande `composer` n'a pas été trouvée, [installez composer](https://getcomposer.org/doc/00-intro.md) ;
- Si vous souhaitez utiliser le serveur de symfony, [installez-le](https://symfony.com/download) ;
- Sur le portail des développeurs discord, dans votre application, cliquez sur *OAuth2* dans la barre latérale. Ajoutez cette redirection `http://127.0.0.1:8000/login/authenticate` ;
- C'est parti ! Mettez en place un serveur web (ou `symfony server:start` en développement) et connectez-vous.

## 🔍 Rapport de bug et suggestions

- 🐛 Vous avez aperçu un bug lorsque vous utilisez Swan Dashboard ?
- 💡 Vous avez une idée ou une suggestion ?
- 💬 Vous souhaitez nous faire part de quelque chose ?

Vous pouvez vous rendre dans le [menu des issues](https://github.com/Romitou/SwanDashboard/issues) et en créer une ; nous y jetterons un œil dès que possible !

## 🔨 Développement et contributions

Nos Pull Request sont ouvertes à toute contribution ! Vous pouvez [créer un fork](https://github.com/Romitou/SwanDashboard/fork) (= une copie) de ce dépôt et y faire vos modifications. Veillez à ajoutez le moins de dépendances possibles.\
N'hésitez pas à venir discuter et tester les nouveautés sur notre [Discord de développement](https://discord.com/njSgX3w) !

### ✅ Tests unitaires

Afin de vérifier que toutes les modifications ne changent pas anormalement le bon fonctionnement de l'application, des tests unitaires ont été écrits via PHPUnit. N'oubliez pas de créer un `.env.test` et d'y remplir toutes les informations en prenant comme exemple `.env.example` et **en modifiant la base de donnée de façon à ce qu'elle ne soit pas identique à celle de développement ou de production** ! Pour réinitialiser et remplir votre base de donnée de test, exécutez `php bin/console doctrine:mongodb:fixtures:load --env=test`. Ensuite, pour lancer les tests unitaires, exécutez simplement `php bin/phpunit` à la racine du projet. N'oubliez pas de remplir à nouveau la base de donnée après chaque test.

### 🤖 Couverture du code

Chaque test unitaire génère un rapport de couverture de code. Celui-ci est un indicateur important faisant référence au nombre de ligne de code exécutées par les tests. En clair, il est nécessaire que chaque partie du code soit exécutée par des tests. Si vous venez à implémenter de nouvelles fonctionnalités, veillez à créer des tests correspondants.

## 🙏 Merci

#### 👥 Développeur

- [Romitou](https://github.com/Romitou) (Romitou#9685)

#### 👷 [Contributeurs](https://github.com/Romitou/SwanDashboard/graphs/contributors)

- [noftaly](https://github.com/noftaly) (noftaly#0359)
