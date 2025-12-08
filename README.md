# CRUD App — Laravel 12 + Vue 3 + PHP 8.4 (Dockerized, MySQL)

[![Laravel](https://img.shields.io/badge/Laravel-12-red)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3-42b883)](https://vuejs.org)
[![PHP](https://img.shields.io/badge/PHP-8.4-777bb4)](https://www.php.net/releases/8.4/en.php)
[![Docker](https://img.shields.io/badge/Docker-Compose-blue)](https://docs.docker.com/compose/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479a1)](https://www.mysql.com/)
[![phpMyAdmin](https://img.shields.io/badge/phpMyAdmin-latest-6c78af)](https://www.phpmyadmin.net/)

A fully containerized Laravel 12 + Vue 3 + PHP 8.4 monorepo setup for rapid local development. Ships with **Nginx**, **PHP-FPM**, **Node.js 22**, **MySQL 8.0**, and **phpMyAdmin** — all wired together via Docker Compose.

See [DOCKER_SETUP.md](DOCKER_SETUP.md) for detailed setup information.

---

## ✨ Tech Stack

* **Backend:** Laravel **12** (PHP 8.4 via PHP-FPM)
* **Frontend:** Vue **3** with **Vite** (Node 22)
* **Web Server:** Nginx (alpine)
* **Database:** MySQL **8.0**
* **DB Admin:** phpMyAdmin
* **Container Runtime:** Docker & Docker Compose

---

## 🧩 Services & Ports

| Service           | Container Name      | Role                   | Port (Host → Container) |
| ----------------- | ------------------- | ---------------------- | ----------------------- |
| Nginx             | `crud_nginx`        | Serves Laravel public/ | **8000 → 80**           |
| PHP-FPM (Laravel) | `crud_app`          | PHP 8.4 app runtime    | (internal: **9000**)    |
| MySQL 8.0         | `crud_mysql`        | Database               | **3307 → 3306**         |
| phpMyAdmin        | `crud_phpmyadmin`   | DB management          | **8080 → 80**           |
| Mailpit           | `crud_mailpit`      | Mail catcher (SMTP/UI) | **1025 → 1025**, **8025 → 8025** |
| Dozzle            | `crud_dozzle`       | Live container logs    | **8890 → 8080**         |

---

## 🚀 Quick Start

```bash
# Start all services
./start.sh

# Or manually
sudo docker compose up -d
```

**Access Points:**
* Laravel: http://localhost:8000
* phpMyAdmin: http://localhost:8080 (root/root)
* Mailpit UI: http://localhost:8025 (SMTP: localhost:1025)
* Dozzle: http://localhost:8890

---

## 🛠️ Common Commands

```bash
# Run artisan commands
sudo docker exec -it crud_app php artisan migrate
sudo docker exec -it crud_app php artisan cache:clear

# Build frontend
sudo docker exec -it crud_app npm run build

# Access container
sudo docker exec -it crud_app bash

# View logs
sudo docker compose logs -f crud_app
```

### Background jobs
- Scheduler runs automatically inside `crud_app` via Supervisor (`laravel-scheduler`).
- Check scheduler output: `sudo docker compose logs -f crud_app | grep scheduler`

### Mail testing
- Mail is routed to Mailpit (`mailpit:1025`).
- View captured mail: http://localhost:8025

### Logs UI
- Live logs for all containers: http://localhost:8890 (Dozzle).

---

## 🔐 Database Credentials

**From Host Machine:**
```bash
mysql -h 127.0.0.1 -P 3307 -u crud_user -psecret crud_db
```

**From Docker Network:**
- Host: `mysql`
- Port: `3306`
- Database: `crud_db`
- User: `crud_user`
- Password: `secret`

---

## 🧯 Troubleshooting

### Permission Errors
```bash
sudo docker exec -it crud_app chown -R www-data:www-data storage bootstrap/cache
sudo docker exec -it crud_app chmod -R 775 storage bootstrap/cache
```

### Build Assets (500 error)
```bash
sudo docker exec -it crud_app npm run build
```

### Reset Everything
```bash
sudo docker compose down -v
sudo docker compose up -d
```

---

For complete documentation, see [DOCKER_SETUP.md](DOCKER_SETUP.md).
