

# LaptopShop - Online Laptop Store

A fully functional online laptop shopping website built with PHP, MySQL, and modern frontend technologies.

## Features

- Responsive design using Bootstrap 5 and Tailwind CSS
- User authentication (login/register)
- Product listing with filters
- Shopping cart functionality
- Checkout system
- Admin panel for product management

## 🖥️ Screenshots

### 📝 Home Page
![Home Page](https://github.com/user-attachments/assets/3f4659d7-afce-402f-b609-49dec862a098)


### 🔐 Login Page
![Login Page](https://github.com/user-attachments/assets/b58ee5e2-e267-44f8-aedd-e9398e5d54ec)


### 🆕 Register Page
![Signup Page](https://github.com/user-attachments/assets/fe4d5c31-aa50-4aa3-afb8-71764bb3f9d8)

### 🛍️ Shop Page
![Shop Page](https://github.com/user-attachments/assets/4a3f2500-d0ad-4306-8be0-adce72d6a53f)


### 🛒 Cart Page
![Cart Page](https://github.com/user-attachments/assets/758e1e10-2f32-4158-9758-c4542b37b63f)


### 🛠️ Admin Dashboard
![Dashboard](https://github.com/user-attachments/assets/d40ed3bd-08fa-410b-8065-51ad9511f654)



## Tech Stack

- Frontend: HTML5, CSS3, Bootstrap 5, Tailwind CSS
- Backend: PHP 
- Database: MySQL
- Server: XAMPP/Apache

## Setup Instructions

1. Install XAMPP (or similar local server environment)
2. Clone this repository to your `htdocs` folder
3. Import the database schema from `database/laptopshop.sql`
4. Configure database connection in `includes/db.php`
5. Start Apache and MySQL services
6. Access the website at `http://localhost/LaptopShop`

## Project Structure

```
LaptopShop/
│
├── index.php             # Home Page
├── shop.php              # All Laptops
├── product.php           # Single Product Page
├── cart.php              # Shopping Cart
├── login.php             # Login Page
├── register.php          # Registration Page
├── checkout.php          # Checkout Page
├── includes/             # PHP Includes
│   ├── db.php            # Database Connection
│   ├── header.php        # Header Template
│   └── footer.php        # Footer Template
├── css/                  # Stylesheets
├── js/                   # JavaScript Files
├── uploads/              # Product Images
└── admin/                # Admin Panel
```

## Database Schema

The project uses the following main tables:
- Users
- Laptops
- Cart
- Orders
- OrderDetails

## License

MIT License 
