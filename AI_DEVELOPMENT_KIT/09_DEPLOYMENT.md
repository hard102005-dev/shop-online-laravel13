# 09 Production Deployment Checklist & Commands

## 1. Pre-Deployment Optimization Protocol

Before deploying to staging or production environments, execute the following commands:

```bash
# 1. Install production dependencies
composer install --no-dev --optimize-autoloader

# 2. Build frontend assets via Vite
npm run build

# 3. Execute database migrations safely
php artisan migrate --force

# 4. Cache framework configuration, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Restart background queue workers
php artisan queue:restart
```

---

## 2. Environment Security Verification
- Ensure `APP_ENV=production` and `APP_DEBUG=false` in production `.env`.
- Verify database password, Redis credentials, and mail server credentials are set.
- Ensure public storage symlink is linked via `php artisan storage:link`.
