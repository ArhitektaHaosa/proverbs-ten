# proverbs-ten

Public website of world proverbs. Content grows organically from Wiktionary seed + visitor contributions.

**Single source of truth:** [AGENTS.md](AGENTS.md)

Temporary domain: proverbs.admin.education (change only via `.env`).

## Scaffold (next local step)

On a machine with PHP 8.4 + Composer:

```bash
# Option A: create fresh Laravel then overlay these files
composer create-project laravel/laravel:^11.0 tmp --prefer-dist
# copy AGENTS.md, docs/, scripts/, .env.example, config/site.php, etc. into it
cd tmp
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
php artisan make:filament-user
```

Then:
- Move `config/site.php` into the Laravel `config/` directory.
- Ensure all site identity reads come from `config('site.*')` or `config('app.url')`.
- Run `scripts/check-domain-agnostic.sh` and keep it green.
- Add legal page skeletons under `resources/pages/` using the placeholders.

## Build order

See AGENTS.md section 18. Finish Scaffold with passing tests before Data model.

Stack: Laravel + Filament, PHP 8.4, MySQL, 2 GB RAM production server, CloudPanel.
