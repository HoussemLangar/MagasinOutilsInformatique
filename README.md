# 🛒 IT Tools Store

A **Symfony application** for managing an **IT tools store**.  
It includes product, client, and order management, along with QR code integration and PDF generation.  

---

## 🚀 Features

- 🛠️ Store management  
- 👥 User management  
- 📦 IT tools management (add, update, delete)  
- 📑 Generate **PDF** entry/exit documents with DomPDF  
- 🔗 Generate and scan **QR Codes**  
- 🗄️ Data persistence with **Doctrine ORM**  

---

## 🛠️ Technologies Used

- [PHP 8+](https://www.php.net/)  
- [Symfony 6](https://symfony.com/) – Main framework  
- [Doctrine ORM](https://www.doctrine-project.org/) – Database ORM  
- [MySQL](https://www.mysql.com/) – Database  
- [DomPDF](https://github.com/dompdf/dompdf) – PDF generation  
- [Endroid/QRCode](https://github.com/endroid/qr-code) – QR codes  
- [Docker](https://www.docker.com/) – Containerization  

---

## 📂 Project Structure

```
MagasinOutilsInformatique/
├── assets/           # Frontend (JS, CSS, Stimulus)
├── bin/              # Symfony console
├── config/           # Symfony config
├── public/           # Entry point (index.php)
├── src/              # Source code (Controllers, Entities, Services)
├── templates/        # Twig views
├── migrations/       # Doctrine migrations
├── tests/            # PHPUnit tests
├── var/              # Cache, logs
├── vendor/           # PHP dependencies
├── .env              # Environment variables
├── composer.json     # Backend dependencies
├── package.json      # Frontend dependencies
└── docker/           # Docker config (Caddy, PHP, MySQL)
```

---

## ⚙️ Installation & Deployment

### 1. Clone the project
```bash
git clone https://github.com/username/MagasinOutilsInformatique.git
cd MagasinOutilsInformatique
```

### 2. Install dependencies
```bash
composer install
npm install
```

### 3. Configure the database
Update the `.env` file with your MySQL credentials:
```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/magasin"
```

Then create the database and run migrations:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. Run the Symfony server
```bash
symfony serve
```
The app will be available at [http://localhost:8000](http://localhost:8000) 🚀  

---

## 🐳 Run with Docker

```bash
docker-compose up --build
```

---

## 🧪 Tests

```bash
php bin/phpunit
```

---

## 📄 License

This project is licensed under the MIT License – feel free to modify and use it.  

---

## 👤 Author

Developed by **Houssem LANGAR**  
📧 Email: houssemlangar3@gmail.com  
