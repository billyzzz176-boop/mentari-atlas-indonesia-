# ERP System (Sales Order & Inventory Management)

A comprehensive web-based Mini ERP application designed to streamline business operations for distribution companies like PT Mentari Atlas Indonesia. Built with Laravel, this system manages sales orders, dynamic inventory tracking, and financial records (receivables and payables).

## Key Features

- **Role-Based Access Control (RBAC):** Secure data privacy ensuring Director, Finance Admin, Warehouse Admin, and Sales agents only access menus relevant to their specific roles.
- **Automated Backorder Logic:** Intelligently handles partial shipments when physical stock is insufficient, automatically calculating remaining debt and updating inventory.
- **Real-time Financial Analytics:** Interactive dashboards displaying profit/loss calculations and outstanding receivables.
- **Premium Printable Documents:** Dynamically generated, aesthetically pleasing corporate invoices and delivery notes built using modern CSS grids.

## Tech Stack

- **Backend:** Laravel (PHP)
- **Database:** MySQL
- **Frontend:** Blade Templates, HTML5, CSS3 (Vanilla / Custom Grid)
- **Architecture:** MVC (Model-View-Controller)

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database settings
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed` (if seeders are available)
6. Run `php artisan serve`

## Author
Sabily Almuhtadi Billah - Full-Stack Web Developer
