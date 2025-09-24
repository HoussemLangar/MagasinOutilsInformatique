# 🛒 Magasin Outils Informatique

Une application **Symfony** permettant la gestion d’un **magasin d’outils informatiques**.  
Elle inclut la gestion des produits, clients, commandes, ainsi que l’intégration de QR codes et la génération de PDF.

---

## 🚀 Fonctionnalités

- 🛠️ Gestion des magasins  
- 👥 Gestion des utilisateurs  
- 📦 Gestion des outils informatiques (ajout, modification, suppression)  
- 📑 Génération de documents d'entrés et sorties en **PDF** avec DomPDF  
- 🔗 Génération et lecture de **QR Codes**  
- 🗄️ Persistance des données via **Doctrine ORM**  

---

## 🛠️ Technologies utilisées

- [PHP 8+](https://www.php.net/)  
- [Symfony 6](https://symfony.com/) – Framework principal  
- [Doctrine ORM](https://www.doctrine-project.org/) – Base de données  
- [MySQL](https://www.mysql.com/) – SGBD  
- [DomPDF](https://github.com/dompdf/dompdf) – Génération de PDF  
- [Endroid/QRCode](https://github.com/endroid/qr-code) – QR codes  
- [Docker](https://www.docker.com/) – Conteneurisation  

---

## 📂 Structure du projet

```
MagasinOutilsInformatique/
├── assets/           # Frontend (JS, CSS, Stimulus)
├── bin/              # Console Symfony
├── config/           # Config Symfony
├── public/           # Point d’entrée (index.php)
├── src/              # Code source (Controllers, Entities, Services)
├── templates/        # Vues Twig
├── migrations/       # Migrations Doctrine
├── tests/            # Tests PHPUnit
├── var/              # Cache, logs
├── vendor/           # Dépendances PHP
├── .env              # Variables d’environnement
├── composer.json     # Dépendances backend
├── package.json      # Dépendances frontend
└── docker/           # Config Docker (Caddy, PHP, MySQL)
```

---

## ⚙️ Installation & Déploiement

### 1. Cloner le projet
```bash
git clone https://github.com/username/MagasinOutilsInformatique.git
cd MagasinOutilsInformatique
```

### 2. Installer les dépendances
```bash
composer install
npm install
```

### 3. Configurer la base de données
Modifier le fichier `.env` pour indiquer vos identifiants MySQL :
```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/magasin"
```

Puis créer la base et lancer les migrations :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. Lancer le serveur Symfony
```bash
symfony serve
```
L’application sera accessible sur [http://localhost:8000](http://localhost:8000) 🚀

---

## 🐳 Exécution avec Docker

```bash
docker-compose up --build
```

---

## 🧪 Tests

```bash
php bin/phpunit
```

---

## 📄 Licence

Ce projet est sous licence MIT – libre à toi de le modifier et l’utiliser.  

---

## 👤 Auteur

Développé par **Houssem LANGAR**  
📧 Email : houssemlangar3@gmail.com  
