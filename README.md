# Lunox Backfire (lunoxhoshizaki-lite)

[![Licence](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg)](https://php.net)

**Lunox Backfire** is an experimental, extremely lightweight, high-performance PHP MVC Framework. It is designed to feel as convenient as large frameworks like Laravel, but is built to be significantly faster, simpler, and less resource-hungry.

This **lunoxhoshizaki-lite** repository serves as the official skeleton for quickly starting your next amazing PHP Web Application or API.

## Core Features

- **Backfire CLI**: Powerful CLI tools for automatically scaffolding Models, Controllers, Middleware, Migrations, and more.
- **Micro ORM**: An Active-Record implementation that keeps database interactions minimal and incredibly fast.
- **Built-In Server**: Instantly run the app via `php backfire serve` during development.
- **Modern Security**: Packed with middlewares protecting against XSS, request forging (CSRF tokens), Clickjacking, and brute-force/DDoS attempts (Rate Limiters).
- **Session & Caching**: Fast encrypted memory sessions with built-in flat-file cache.

---

## 🚀 Getting Started

To get started quickly, follow these steps to clone and bootstrap your new project:

### 1. Clone the Skeleton Repository
Clone the repository and replace `my-app` with your desired project directory name:
```bash
git clone https://github.com/lunox-61/lunoxhoshizaki-lite.git my-app
cd my-app
```

### 2. Install Dependencies
Make sure you have [Composer](https://getcomposer.org/) installed, then run:
```bash
composer install
```

### 3. Dump Autoload File
Regenerate your class mappings to ensure everything loads optimally:
```bash
composer dump-autoload -o
```

### 4. Setup Environment
Copy the `.env.example` file and configure your database parameters:
```bash
cp .env.example .env
```
*(Open `.env` in your editor and input your local MySQL/SQLite credentials)*

### 5. Start Development Server
Boot up the Backfire built-in web server:
```bash
php backfire serve
```
Your brand new application is now alive at `http://localhost:8000`! 🎉

---

## 📖 Documentation
Detailed framework documentation is integrated safely into the views routing layer. To read the full documentation (Routing, Views, Database, Requests, Error Handling), ensure your server is running and head directly to: 

👉 `http://localhost:8000/docs/installation`

If you are looking for release changelogs and detailed framework history, please view the [VERSIONING.md](resources/docs/VERSIONING.md) file.

## 🤝 Contributing
Want to contribute to Lunox Backfire and make it even faster? Pull Requests are highly welcomed! Please ensure you open an Issue first discussing any major architectural changes.

## 🛡️ License
The Lunox Backfire framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
