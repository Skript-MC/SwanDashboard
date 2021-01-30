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


## 🙏 Merci

#### 👥 Développeur

- [Romitou](https://github.com/Romitou) (Romitou#9685)

#### 👷 [Contributeurs](https://github.com/Romitou/SwanDashboard/graphs/contributors)

- [noftaly](https://github.com/noftaly) (noftaly#0359)
