# Mega School Plaza - Shop Rental & Management System

A complete web application for managing shop rentals in a commercial plaza, built with pure PHP, MySQL, HTML, CSS, and JavaScript.

## Features

- **Public Shop Catalog**: Browse and search available shops with filters
- **User Management**: Role-based access control (Super Admin, Manager, Tenant)
- **Application System**: Online shop rental applications with approval workflow
- **Lease Management**: Complete lease lifecycle from draft to termination
- **Billing & Invoicing**: Automated invoice generation and payment tracking
- **Maintenance Tickets**: Issue reporting and tracking system
- **Reports & Analytics**: Occupancy rates, revenue reports, and data exports
- **Audit Logging**: Complete activity tracking for security and compliance

## Technology Stack

- **Frontend**: Pure HTML5, CSS3, Vanilla JavaScript (ES6)
- **Backend**: PHP 8+ with PDO
- **Database**: MySQL/MariaDB
- **Architecture**: MVC pattern with custom routing

## Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or MariaDB 10.3+
- Web server (Apache/Nginx)
- mod_rewrite enabled (for Apache)

## Installation

1. **Clone or download the project**
   \`\`\`bash
   git clone <repository-url>
   cd mega-school-plaza
   \`\`\`

2. **Configure your web server**
   - Set document root to `/public` directory
   - Ensure mod_rewrite is enabled (Apache)
   - Configure URL rewriting (Nginx)

3. **Database Setup**
   \`\`\`bash
   # Create database and import schema
   mysql -u root -p
   \`\`\`
   \`\`\`sql
   CREATE DATABASE megaplaza CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   exit;
   \`\`\`
   \`\`\`bash
   mysql -u root -p megaplaza < database/megaplaza_seed.sql
   \`\`\`

4. **Configure Application**
   - Copy and edit `/config/config.php`
   - Update database credentials:
     \`\`\`php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'megaplaza');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     \`\`\`
   - Update BASE_URL to match your domain:
     \`\`\`php
     define('BASE_URL', 'http://your-domain.com');
     \`\`\`

5. **Set Permissions**
   \`\`\`bash
   chmod -R 755 storage/
   chmod -R 755 storage/uploads/
   chmod -R 755 storage/logs/
   \`\`\`

6. **Email Configuration (Optional)**
   - Update SMTP settings in `/config/config.php`
   - Set `MAIL_ENABLED` to `true` when configured

## Default Accounts

After importing the seed data, you can login with these accounts:

### Super Admin
- **Email**: admin@megaplaza.com
- **Password**: password

### Manager
- **Email**: manager1@megaplaza.com
- **Password**: password

### Tenant
- **Email**: tenant1@example.com
- **Password**: password

> **Note**: Change all default passwords immediately after installation!

## Demo Flow

1. **Public Browsing**
   - Visit `/` to see the homepage
   - Browse shops at `/shops`
   - Use filters to find specific shop types

2. **Tenant Registration**
   - Register a new tenant account
   - Browse available shops
   - Submit an application for a shop

3. **Manager Approval**
   - Login as manager
   - Review applications at `/admin/applications`
   - Approve an application (creates draft lease)

4. **Lease Activation**
   - Activate the lease at `/admin/leases`
   - Shop status changes to "Occupied"
   - Invoice is automatically generated

5. **Payment Processing**
   - Record payment at `/admin/invoices`
   - Generate receipt
   - Update tenant balance

6. **Maintenance Tickets**
   - Tenant creates ticket at `/tenant/tickets`
   - Manager tracks and updates status
   - Email notifications sent

## File Structure

\`\`\`
/
├── public/                 # Web root
│   ├── index.php          # Application entry point
│   ├── .htaccess          # Apache rewrite rules
│   └── assets/            # CSS, JS, images
├── app/
│   ├── Controllers/       # Request handlers
│   ├── Models/           # Data models
│   ├── Views/            # HTML templates
│   ├── Middleware/       # Request middleware
│   ├── Services/         # Business logic
│   └── Helpers/          # Utility classes
├── config/
│   ├── config.php        # Main configuration
│   ├── db.php           # Database connection
│   └── routes.php       # URL routing
├── storage/
│   ├── uploads/         # File uploads
│   └── logs/           # Application logs
└── database/
    └── megaplaza_seed.sql # Database schema & seed data
\`\`\`

## API Endpoints

- `GET /api/shops` - Shop listings with filters
- `GET /api/dashboard-stats` - Dashboard statistics
- `GET /cron?key=secret` - Automated tasks (invoice generation)

## Security Features

- CSRF protection on all forms
- SQL injection prevention with PDO prepared statements
- XSS protection with output escaping
- File upload validation and security
- Role-based access control
- Session security with regeneration
- Rate limiting on authentication

## Customization

### Adding New Shop Categories
1. Insert into `categories` table
2. Update filters in shop catalog
3. Add category-specific business logic if needed

### Modifying Invoice Templates
- Edit `/app/Views/invoices/template.php`
- Update CSS in `/public/assets/css/print.css`

### Adding Payment Methods
1. Update `payments.method` enum in database
2. Modify payment forms and processing logic
3. Update reporting queries

## Troubleshooting

### Common Issues

1. **404 Errors on all pages**
   - Check web server configuration
   - Ensure mod_rewrite is enabled
   - Verify .htaccess file exists in /public

2. **Database Connection Failed**
   - Verify database credentials in config.php
   - Ensure MySQL service is running
   - Check database exists and user has permissions

3. **File Upload Errors**
   - Check storage/ directory permissions
   - Verify PHP upload_max_filesize setting
   - Ensure .htaccess exists in storage/uploads/

4. **Email Not Sending**
   - Check SMTP configuration
   - Verify MAIL_ENABLED is true
   - Check logs in storage/logs/email.log

### Debug Mode

Enable debug mode in `/config/config.php`:
\`\`\`php
define('APP_DEBUG', true);
\`\`\`

This will show detailed error messages and stack traces.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions:
- Check the troubleshooting section
- Review the code documentation
- Create an issue in the repository

---

**Mega School Plaza** - Professional shop rental management made simple.
