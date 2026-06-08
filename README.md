# Khati Roll Express CMS

## About the Project

Khati Roll Express CMS is a simple web application developed using PHP and MySQL. The main purpose of this project is to manage food menu items digitally for a Khati Roll shop or food kiosk.

Using this system, the admin can add new menu items, update existing items, remove unavailable products, and search food items easily. Customers can view the available menu along with prices and images.

This project was developed as part of the CA2 Activity for Web Development Using PHP.

---

## Features

* Admin Login
* Add Menu Items
* Edit Menu Items
* Delete Menu Items
* Upload Food Images
* Search Menu Items
* Filter by Category
* Responsive User Interface

---

## Technologies Used

* HTML
* CSS
* Bootstrap
* PHP
* MySQL
* XAMPP



## Project Structure


khati-roll/
│
├── index.php
├── menu.php
├── search.php
├── db.php
│
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── add_item.php
│   ├── edit_item.php
│   └── delete_item.php
│
├── uploads/
└── assets/


## Database Tables

### admin_users

Stores admin login information.

### categories

Stores different food categories such as:

* Veg Rolls
* Chicken Rolls
* Egg Rolls
* Drinks

### menu_items

Stores menu details including:

* Item Name
* Description
* Price
* Category
* Image

---

## How to Run the Project

1. Install XAMPP.
2. Copy the project folder into the htdocs folder.
3. Create a database named `khati_roll`.
4. Import the SQL file.
5. Update database details in `db.php`.
6. Start Apache and MySQL.
7. Open the browser it.

## Future Improvements

* Online Ordering System
* Customer Login
* Cart Feature
* Online Payment Integration
* Customer Reviews

---

## Conclusion

This project helped me understand how CRUD operations work using PHP and MySQL. It also provided practical experience in database management, form handling, image uploads, and search functionality.

Thank You.
