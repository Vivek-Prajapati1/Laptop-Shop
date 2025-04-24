# LaptopShop - Online Laptop Store

A fully functional online laptop shopping website built with PHP, MySQL, and modern frontend technologies.

## Features

- Responsive design using Bootstrap 5 and Tailwind CSS
- User authentication (login/register)
- Product listing with filters
- Shopping cart functionality
- Checkout system
- Admin panel for product management

## Tech Stack

- Frontend: HTML5, CSS3, Bootstrap 5, Tailwind CSS
- Backend: PHP (Vanilla)
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