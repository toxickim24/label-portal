# Label Client Portal - Setup & Installation Guide

This guide will walk you through setting up the Label Client Portal on your local development environment or production server.

---

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Installation Steps](#installation-steps)
3. [Configuration](#configuration)
4. [Database Setup](#database-setup)
5. [Running the Application](#running-the-application)
6. [Default Credentials](#default-credentials)
7. [Troubleshooting](#troubleshooting)
8. [Production Deployment](#production-deployment)

---

## System Requirements

### Required Software

- **PHP**: 8.3.14 or higher
- **Composer**: Latest version
- **Node.js**: 18.x or higher
- **NPM**: 9.x or higher
- **MySQL**: 8.0 or higher
- **Web Server**: Apache or Nginx

### Recommended Development Environment

- **Windows**: WAMP64 / XAMPP
- **macOS**: Laravel Valet / MAMP
- **Linux**: LAMP Stack

### PHP Extensions Required

```
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GD or Imagick (for image processing)
```

---

## Installation Steps

### 1. Clone the Repository

```bash
# Clone from GitHub
git clone https://github.com/toxickim24/label-portal.git

# Navigate to project directory
cd label-portal
```

### 2. Install PHP Dependencies

```bash
# Install Composer dependencies
composer install
```

**Note**: If you encounter path issues on Windows, ensure PHP is in your PATH or use the full path:
```bash
/c/wamp64/bin/php/php8.3.14/php /path/to/composer.phar install
```

### 3. Install JavaScript Dependencies

```bash
# Install NPM packages
npm install
```

### 4. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env
```

Edit `.env` file and configure the following:

```env
APP_NAME="Label Portal"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=label_portal
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@labelportal.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

This will automatically set the `APP_KEY` in your `.env` file.

---

## Database Setup

### 1. Create Database

**Using MySQL Command Line:**
```sql
CREATE DATABASE label_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Using phpMyAdmin:**
1. Open phpMyAdmin
2. Click "New" to create a new database
3. Name it `label_portal`
4. Select `utf8mb4_unicode_ci` as collation
5. Click "Create"

### 2. Run Migrations

```bash
# Run all database migrations
php artisan migrate
```

This will create all necessary tables:
- users
- roles and permissions (Spatie)
- settings
- activity_log
- cache, jobs, sessions, password_reset_tokens

### 3. Seed the Database

```bash
# Run all seeders
php artisan db:seed
```

This will create:
- Default roles (admin, manager, user)
- Default admin user
- Basic permissions

**OR run fresh migration with seeding:**
```bash
php artisan migrate:fresh --seed
```

⚠️ **Warning**: `migrate:fresh` will drop all tables and recreate them. Only use on initial setup!

---

## Configuration

### Storage & Cache

```bash
# Create symbolic link for public storage
php artisan storage:link

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### File Permissions (Linux/macOS)

```bash
# Set proper permissions for storage and cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Build Assets

```bash
# Build frontend assets for development
npm run dev

# OR build for production
npm run build
```

---

## Running the Application

### Development Mode

You need to run TWO commands in separate terminals:

**Terminal 1 - Laravel Development Server:**
```bash
php artisan serve
```
This will start the server at: http://127.0.0.1:8000

**Terminal 2 - Vite Development Server:**
```bash
npm run dev
```
This will start Vite at: http://localhost:5173

### Access the Application

Open your browser and navigate to:
```
http://127.0.0.1:8000
```

You will be redirected to the login page.

### Quick Start Script (Windows)

Create a file `start-dev.bat`:
```batch
@echo off
start cmd /k "cd C:\wamp64\www\label-portal && npm run dev"
start cmd /k "cd C:\wamp64\www\label-portal && php artisan serve"
```

Double-click to run both servers at once!

### Quick Start Script (macOS/Linux)

Create a file `start-dev.sh`:
```bash
#!/bin/bash
npm run dev &
php artisan serve
```

Make it executable and run:
```bash
chmod +x start-dev.sh
./start-dev.sh
```

---

## Default Credentials

After running the seeders, use these credentials to log in:

### Admin Account
- **Email**: `admin@labelsalesagents.com`
- **Password**: `Thelabel99!`

⚠️ **Important**: Change the admin password immediately after first login!

---

## Troubleshooting

### Issue: "No application encryption key has been specified"

**Solution:**
```bash
php artisan key:generate
```

### Issue: Database connection refused

**Solution:**
1. Ensure MySQL is running
2. Check database credentials in `.env`
3. Verify database exists
4. Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

### Issue: "Class 'X' not found"

**Solution:**
```bash
composer dump-autoload
php artisan clear-compiled
```

### Issue: Vite manifest not found

**Solution:**
```bash
npm install
npm run build
```

### Issue: Permission denied (storage/logs)

**Solution (Linux/macOS):**
```bash
chmod -R 775 storage
chown -R www-data:www-data storage
```

**Solution (Windows):**
- Right-click `storage` folder
- Properties → Security → Edit
- Give full control to your user

### Issue: Mixed content errors (https/http)

**Solution:**
Update `.env`:
```env
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com
```

### Issue: 500 Error after deployment

**Solution:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

### Issue: Images not uploading

**Solution:**
1. Check storage is linked: `php artisan storage:link`
2. Verify upload limits in `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```
3. Restart web server

---

## Production Deployment

### Preparation Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set proper `APP_URL` in `.env`
- [ ] Configure proper database credentials
- [ ] Set up email service (SendGrid, Mailgun, etc.)
- [ ] Configure HTTPS/SSL certificate
- [ ] Set up automated backups
- [ ] Configure log rotation

### Build for Production

```bash
# Install dependencies (production only)
composer install --optimize-autoloader --no-dev

# Build assets
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run migrations (if needed)
php artisan migrate --force
```

### Environment Variables

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=label_portal
DB_USERNAME=your-db-user
DB_PASSWORD=your-secure-password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

SESSION_DRIVER=database
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Web Server Configuration

#### Apache (.htaccess)

The `.htaccess` file is included in `public/` directory.

#### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/label-portal/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Security Recommendations

1. **HTTPS Only**
   ```env
   SESSION_SECURE_COOKIE=true
   ```

2. **Strong Database Password**
   - Use 20+ character random password
   - Include uppercase, lowercase, numbers, symbols

3. **Regular Updates**
   ```bash
   composer update
   npm update
   ```

4. **Firewall Configuration**
   - Only allow ports 80, 443, and necessary DB ports
   - Restrict database access to localhost

5. **Backup Strategy**
   - Daily database backups
   - Weekly full backups
   - Off-site backup storage

### Queue Worker (Optional)

If using queues in production:

```bash
# Install Supervisor
apt-get install supervisor

# Create config: /etc/supervisor/conf.d/label-portal.conf
[program:label-portal-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/label-portal/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/label-portal/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Start supervisor
supervisorctl reread
supervisorctl update
supervisorctl start label-portal-worker:*
```

### Task Scheduling

Add to crontab:
```bash
* * * * * cd /var/www/label-portal && php artisan schedule:run >> /dev/null 2>&1
```

---

## Updating the Application

### Pull Latest Changes

```bash
git pull origin main
```

### Update Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### Run Migrations

```bash
php artisan migrate --force
```

### Clear Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Restart Services

```bash
# Restart queue workers
php artisan queue:restart

# Restart web server (if needed)
sudo service nginx restart
# OR
sudo service apache2 restart
```

---

## Additional Configuration

### Email Service Setup (Production)

#### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-api-key
```

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
```

### File Storage (S3)

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

## Development Tips

### Useful Commands

```bash
# Clear everything
php artisan optimize:clear

# Create new migration
php artisan make:migration create_table_name

# Create new controller
php artisan make:controller ControllerName

# Create new model
php artisan make:model ModelName -mcr

# Run tests
php artisan test

# Generate IDE helper (for better autocomplete)
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
```

### Database Management

```bash
# Fresh migration (WARNING: deletes all data)
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Show migration status
php artisan migrate:status
```

### Tinker (Laravel REPL)

```bash
php artisan tinker

# Example: Create user
User::create(['name' => 'Test', 'email' => 'test@example.com', 'password' => bcrypt('password')]);

# Example: Check roles
User::find(1)->roles;
```

---

## Support & Resources

### Documentation
- [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) - Detailed project overview
- [PHASES.md](PHASES.md) - Project phases and roadmap

### External Resources
- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Vue 3 Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Tailwind CSS Documentation](https://tailwindcss.com/)

### Getting Help
- GitHub Issues: https://github.com/toxickim24/label-portal/issues
- Laravel Community: https://laracasts.com/discuss

---

**Last Updated**: March 23, 2026
**Document Version**: 1.0.0
**Maintained By**: Development Team
