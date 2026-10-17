# Deployment (Debian 13 + CloudPanel, 2 GB RAM)

Keep this file accurate. Any change to env, queue, cron or caching must update it.

## 1. First deploy

1. CloudPanel: create a **PHP site** (Laravel template if offered), PHP 8.4, domain from `APP_URL`. Document root is `public`.
2. CloudPanel: create the MySQL database and user. Put credentials in `.env`.
3. Clone `proverbs-ten` into the site directory as the site user.
4. `composer install --no-dev --optimize-autoloader`
5. Copy `.env.example` to `.env`, fill it, then `php artisan key:generate`.
6. `php artisan migrate --force`
7. Frontend assets are built elsewhere (dev machine or CI) and deployed in `public/build`. Do not run npm on the server.
8. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
9. Create the single admin user (owner), enable 2FA in Filament.
10. Add Let's Encrypt SSL in CloudPanel.

## 2. Queue worker (Supervisor)

Install if missing: `apt install supervisor`. Example `/etc/supervisor/conf.d/proverbs-worker.conf` (adjust paths and user):

```
[program:proverbs-worker]
command=php /home/SITEUSER/htdocs/SITEDOMAIN/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --memory=128
user=SITEUSER
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=3600
redirect_stderr=true
stdout_logfile=/home/SITEUSER/logs/worker.log
```

Then `supervisorctl reread && supervisorctl update`. One worker only on 2 GB.
After each deploy: `php artisan queue:restart`.

## 3. Scheduler (cron)

CloudPanel cron job for the site user, every minute:

```
* * * * * php /home/SITEUSER/htdocs/SITEDOMAIN/artisan schedule:run >> /dev/null 2>&1
```

The scheduler runs: Proverb of the Day filler, sitemap build, daily AI draft batch (respects `AI_DAILY_LIMIT`) and leaderboard cache refresh. Database backups run from a separate cron job (section 5).

## 4. Caching

Public GET pages are cacheable and set no cookies. Use the web-server cache available in CloudPanel (Nginx cache or Varnish, per the site settings). Purge with `php artisan site:purge-cache` (called automatically on approval or unpublish). Forms fetch tokens from an uncached endpoint, so keep that route excluded from the cache.

## 5. Backups

Nightly `mysqldump` of the database to a location outside the web root, kept for at least 14 days, plus a copy off the server. Test a restore once before launch.

## 6. Updating the site

```
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan queue:restart
php artisan site:purge-cache
```

## 7. Changing the domain

1. CloudPanel: add the new domain to the site and issue SSL.
2. Edit `.env`: `APP_URL`, `SITE_NAME`, `SITE_TAGLINE`, `CONTACT_EMAIL` if needed.
3. `php artisan config:cache`, then `php artisan sitemap:build` and `php artisan site:purge-cache`.
4. Old domain: 301 redirect every path to the same path on the new domain (CloudPanel vhost or redirect rule).
5. CMP: the CMP configuration is bound to the domain. Create or update it for the new domain and set the new `CMP_ID`.
6. GA4: add the new domain as a data stream or update the existing one; update `GA4_MEASUREMENT_ID` if it changed.
7. AdSense: approval is per domain. Apply with the final domain only. Then set `ADSENSE_CLIENT`, `ADSENSE_ENABLED=true`, and switch `CMP_PROVIDER` to the Google-certified option.
8. Search Console: add the new property, submit the sitemap, use the Change of Address tool if available.
9. Set `SITE_INDEXABLE=true` only when the final domain is ready to be indexed.
10. Run `scripts/check-domain-agnostic.sh` to confirm nothing hardcoded the old domain.

## 8. Launch checklist (owner)

- [ ] `resources/pages/*.md` reviewed by the owner (they are templates, not legal advice).
- [ ] Privacy page matches what the site really does.
- [ ] `CMP_ID` set; consent banner appears; GA4 fires only after consent.
- [ ] `ADSENSE_ENABLED=false` until the final domain is approved.
- [ ] `SITE_INDEXABLE` set deliberately (false on the temporary domain).
- [ ] Free AI tier limits re-checked for Groq and OpenRouter; `AI_DAILY_LIMIT` set accordingly.
- [ ] Backups tested.
- [ ] Filament 2FA enabled; admin path changed from default if desired.
