# proverbs-ten

Public website of world proverbs. Content grows organically from Wiktionary seed + visitor contributions.

**Single source of truth:** [AGENTS.md](AGENTS.md)

Temporary domain: proverbs.admin.education (change only via `.env`).

## Build order

See AGENTS.md section 18. Start with Scaffold (Laravel + Filament + domain guard).

Run `scripts/check-domain-agnostic.sh` before every commit.

Stack: Laravel + Filament, PHP 8.4, MySQL, 2 GB RAM production server, CloudPanel.
