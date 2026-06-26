# Lake View Sweets & Bakery - E-commerce Website

A full-featured e-commerce website for Lake View Sweets & Bakery built with Laravel, Vue 3, Inertia.js, Tailwind CSS, and MySQL.

## Features

### Customer Features
- **Home Page** - Hero banner, category showcase, featured products, branch listing
- **Product Listing** - Browse all products with category filter and search
- **Product Details** - Individual product page with related products
- **Shopping Cart** - localStorage-based cart with quantity management
- **Checkout** - Cash on delivery, pickup or home delivery, coupon system
- **Order Tracking** - Track orders by order number
- **Custom Cake Ordering** - Upload design image, specify cake type/size/flavor, delivery date
- **About & Contact** - Information pages with branch details

### Admin Panel
- **Dashboard** - Stats overview (orders, revenue, products, etc.)
- **Products** - Full CRUD with image upload, discount pricing, featured flag
- **Categories** - Full CRUD with sort ordering
- **Branches** - Full CRUD with multiple phone numbers
- **Delivery Areas** - Manage delivery zones and charges per branch
- **Orders** - View and update order status
- **Custom Cake Orders** - View designs, update status and estimated price
- **Coupons** - Full CRUD for percentage/fixed discount coupons
- **Settings** - Manage site name, hero text, about text, social links, etc.

## Tech Stack
- **Backend**: Laravel 12
- **Frontend**: Vue 3 + Inertia.js
- **Styling**: Tailwind CSS v4
- **Database**: MySQL (UTF8MB4)
- **Font**: Inter (Google Fonts)

## Setup Instructions

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer

### Installation

1. **Install PHP dependencies**
   ```bash
   composer install
   ```

2. **Install Node dependencies**
   ```bash
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` and set your MySQL database credentials:
   ```
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```
   For development with hot reload:
   ```bash
   npm run dev
   ```

7. **Start the server**
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000`

## Admin Access

- **URL**: `http://localhost:8000/login`
- **Email**: `admin@lakeview.com`
- **Password**: `admin123`

## Data Seeded

- **8 Categories**: Fast Food, Bread, Cookies, Toast, Dessert, Cake, Order Cake, Sweets
- **100+ Products**: All products from the Excel sheet with English names
- **7 Branches**: All branch locations with phone numbers
- **14 Delivery Areas**: Sadar (৳30) and Outside Sadar (৳60) per branch
- **2 Coupons**: WELCOME10 (10% off, min ৳200), FLAT50 (৳50 off, min ৳500)
- **13 Settings**: Site name, hero text, about text, social links, etc.

## Order Flow

1. Customer browses products and adds to cart
2. Customer goes to checkout, selects branch and delivery type
3. Optional coupon code can be applied
4. Order is placed with Cash on Delivery
5. Admin can view and update order status from admin panel
6. Customer can track order by order number

## Custom Cake Flow

1. Customer fills out custom cake form with details and design image
2. Order is submitted with "pending" status
3. Admin reviews the design and sets estimated price
4. Admin updates status as the cake is prepared
