# Modern E-Commerce Platform

A modern PHP e-commerce platform with a clean architecture and secure implementation.

## Project Structure

```
e-commerce/
├── config/               # Configuration files
├── src/                 # Source code
│   ├── Models/         # Database models
│   ├── Controllers/    # Request handlers
│   └── Services/       # Business logic
├── public/             # Public files
│   ├── assets/        # Static assets
│   └── index.php      # Entry point
├── views/              # View templates
├── includes/           # Shared PHP files
└── utils/             # Helper functions
```

## Setup Instructions

1. Clone the repository
2. Create a `.env` file in the root directory with the following content:
   ```
   DB_HOST=localhost
   DB_NAME=ecommerce_db
   DB_USER=root
   DB_PASS=
   
   APP_NAME=E-Commerce
   APP_ENV=development
   APP_DEBUG=true
   APP_URL=http://localhost
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

4. Import the database schema:
   ```bash
   mysql -u root < DATABASE.sql
   ```

5. Configure your web server to point to the `public` directory

6. Ensure the following PHP extensions are enabled:
   - PDO
   - PDO_MySQL
   - mbstring
   - json

## Features

- Modern MVC Architecture
- Secure Authentication System
- Product Management
- Shopping Cart
- Order Processing
- Admin Dashboard
- User Management

## Security Features

- Password Hashing
- CSRF Protection
- SQL Injection Prevention
- XSS Protection
- Session Security

## Development

1. Follow PSR-4 autoloading standards
2. Use prepared statements for all database queries
3. Validate all user inputs
4. Handle errors gracefully
5. Document your code

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License.