# 🏠 Natty Home Appliances Store

## Project Overview
Natty Home Appliances Store is a web-based e-commerce application developed using Object-Oriented Programming (OOP) in PHP. The system allows customers to browse home appliances, register, log in, add products to the cart, place orders, and manage their profiles. Administrators can manage products, categories, and customer orders through an admin dashboard.

## Objectives
- Provide an online platform for selling home appliances.
- Allow customers to order products easily.
- Enable administrators to manage products and orders.
- Apply Object-Oriented Programming (OOP) concepts in web development.

## Technologies Used
- PHP (OOP)
- MySQL
- HTML5
- CSS3
- JavaScript
- PDO (PHP Data Objects)
- XAMPP

## Main Features

### Customer
- User Registration
- User Login
- View Products
- Browse Products by Category
- Add Products to Cart
- Place Orders
- View Profile
- Update Profile
- Contact Page
- About Page

### Administrator
- Secure Admin Login
- Dashboard
- Manage Products
- Add Products
- Edit Products
- Delete Products
- Manage Orders
- View Order Details

## Product Categories
- Refrigerators & Freezers
- Laundry & Cleaning Appliances
- Kitchen & Cooking Appliances
- TV & Audio Systems
- Fans & Air Cooling

## Database Tables
- users
- categories
- products
- cart_items
- orders
- order_items
- payments
- activity_logs

## Installation Guide

1. Install XAMPP.
2. Copy the project folder into:
   ```
   htdocs/
   ```
3. Start Apache and MySQL.
4. Open phpMyAdmin.
5. Create a database named:
   ```
   home_appliance_store
   ```
6. Import the SQL file included in the project.
7. Open your browser and visit:
   ```
   http://localhost/home-appliance-store/public/
   ```

## Admin Login

Email:
```
admin@home.com
```

Password:
```
(admin123)
```

## Project Structure

```
home-appliance-store/
│
├── classes/
├── config/
├── public/
│   ├── assets/
│   ├── api/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── profile.php
│   ├── cart.php
│   ├── checkout.php
│   ├── about.php
│   ├── contact.php
│   └── admin_dashboard.php
│
├── database/
├── README.md
└── home_appliance_store.sql
```

## Future Improvements
- Online Payment Integration
- Email Notifications
- Product Search
- Product Reviews
- Wishlist
- Sales Reports
- Mobile Responsive Design

## Developer

**Name:** Asnath-Catherine Godfrey Gumbo

**Course:** Information and Communication Technology (ICT)

## License

This project was developed for academic purposes only.