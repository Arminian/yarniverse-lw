# Yarniverse e-commerce platform

E-commerce fullstack web project using Laravel 13 and Livewire 4. 

Web demo is available [here](https://yarniverse-emwt.onrender.com/)


## 💡 Features
1. User authentication (registration, login, password reset)
2. Role-based access control (Admin, Regular User) with permission management
3. Product catalog: sorting (by price, newest and more) and configurable default sort
4. Advanced product filtering (category, price range, attributes, availability, search)
5. Shopping cart management (add/update/remove items, quantity adjustments, persisted cart)
6. User account management (profile update, address book, order history)
7. Order processing and checkout workflow (order summary, shipping options, tax calculation)
8. Payment integration with Stripe (secure tokenization, saved payment methods, webhooks for payment events)
9. Admin dashboard with full CRUD access to database entities (products, users, orders, reviews)
10. Admin analytics and reporting (sales metrics, inventory status, user activity)
11. Moderation controls: admin approval for product reviews and manual order confirmation

## 💿 Important Stack
- Laravel framework
- Filament
- Livewire + Livewire Flux
- Fortify, Spatie Permission, Filament Shield
- Stripe PHP SDK
- Vite, Tailwind CSS, laravel-vite-plugin

## Versioning
| App | PHP | Laravel | Livewire | Livewire Flux | Filament | Tailwind CSS | Vite |
| :-----: | :-----: | :-----: | :-----: | :-----: | :-----: | :-----: | :-----: |  
| v1.00 | 8.4 | 13.7 | 4.1 | 2.13.1 | 5.0 | 4.0.7 | 8.0.0 |

## ⚙️ Quick Start

To build and run:

```bash
# Install laravel dependencies
composer install --no-dev

# Install js modules
npm install

# Build the project
npm run build

# Add the database
php artisan migrate

# Run in dev
php artisan serve && npm run dev
```

## 📸 Screenshots
<img src=https://i.postimg.cc/662WVRj1/Screenshot-20260507-104042.png alt="Home page">

<img src=https://i.postimg.cc/QN98g54Y/Screenshot-20260507-104117.png alt="Products catalog">

<img src=https://i.postimg.cc/gcCYgJQV/Screenshot-20260507-104300.png alt="User dashboard">

---

Built for demonstration 🛐 with Laravel 13.