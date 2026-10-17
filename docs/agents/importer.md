# Role: importer

Read `AGENTS.md` first. This file adds role detail; `AGENTS.md` wins on conflict.

## Mission
Fill the database with attributed, deduplicated proverbs from Wiktionary (and optionally public-domain collections) without exhausting the 2 GB server or violating Wikimedia etiquette.

## Owns
`app/Console/Commands/ProverbsRankLanguages.php`, `ProverbsImport.php`, `app/Services/Import/*`, `config/languages.php`, importer tests.

## Commands
- `php artisan proverbs:rank-languages [--top=40]`
  Counts members of Wiktionary categories named "`<Language> proverbs`" through the MediaWiki API (category info / member counts), prints a ranked table (language, code, category, count). **Writes nothing.** The owner picks the top 10; those go into `config/languages.php` with `import_rank`.
- `php artisan proverbs:import {lang} [--limit=] [--resume]`
  Fetches category members in batches, parses the entry, normalizes text, inserts as `draft`.

## Rules
1. Idempotent: dedup on (`language_id`, `text_hash`) where `text_hash = sha256(NFC-normalized, case-folded, whitespace-collapsed text)`. Re-running updates revision metadata, never duplicates.
2. Store for every entry: `source_type=wiktionary`, `source_url`, `source_title`, `source_revision`, `license` (verify the current Wiktionary content license and store it), `attribution`.
3. Imported entries are `draft`. Never auto-approve.
4. API etiquette: descriptive User-Agent including `WIKTIONARY_CONTACT` (fallback `CONTACT_EMAIL`), serial requests with a delay, honor `maxlag`, handle `continue` tokens, back off on 429/5xx. For bulk volume prefer official dumps over the live API.
5. Memory: stream and chunk. Never hold a whole category in memory. Support `--resume` through a stored cursor.
6. Parsing must tolerate missing definitions, templates, and alternative forms. Skip entries that are clearly not proverbs (single words, phrases without sentence structure) and log them to a `import_skips` table or log channel with the reason.
7. Unicode: NFC, strip control and zero-width junk, keep the original script. Detect direction for RTL.
8. Do not translate or enrich here. Enrichment belongs to the ai-drafter role.
9. Public-domain sources: record the exact source, edition and why it is public domain before importing.
10. Tests use recorded fixtures. Never call Wikimedia from tests.

## Done when
- Ranking command works against fixtures, import is idempotent (test imports twice), resume works, attribution fields always filled, `composer test` green.
- Report: number imported per language, skipped count and reasons, anything the owner must decide.
