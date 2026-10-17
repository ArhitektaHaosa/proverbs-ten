# proverbs-ten — Master Agent Instructions

Single source of truth for Claude Code, Cursor, OpenCode and any other coding agent.
`CLAUDE.md`, `.cursor/rules/*` and `opencode.json` only point here. Never copy rules into those files; edit this file.

Language: all code, comments, UI copy, commits and docs are in **English**.

---

## 1. Product

A public website of **world proverbs** whose content grows organically.

- Seed content is imported from Wiktionary (proverb categories per language) with attribution.
- Visitors **build on** proverbs instead of only adding new ones: translation, literal meaning, equivalent in another language, usage example, origin story, modern remix.
- Proverbs that express the same thought are grouped into an **Idea** (e.g. "patience pays off") and shown as "the same thought in N languages". This graph is the main SEO and retention asset.
- Daily features: **Proverb of the Day** and a **Daily Quiz** with a leaderboard.
- Monetization: Google AdSense (**off at start**). Analytics: Google Analytics 4.
- Content quality beats volume. Thin or unreviewed pages must never be indexable.

Working domain: `proverbs.admin.education` (temporary). **The code must not care which domain it runs on** (see section 4).
Repo: `proverbs-ten`. Contact: `proverbs@admin.education` (via `CONTACT_EMAIL`).
There is exactly one human moderator (the owner). Design every workflow around that bottleneck.

## 2. Stack (fixed, do not swap without asking)

- Debian 13, CloudPanel, Nginx, PHP 8.4, MySQL. **Production server has only 2 GB RAM.**
- Laravel (latest stable that supports PHP 8.4) + **Filament** (latest stable compatible with that Laravel) for the admin/moderation panel.
- Public site: Blade templates + Tailwind + minimal vanilla JS or Alpine. No SPA framework. Server-rendered HTML first.
- Queue: `database` driver, one worker (Supervisor). Cache: `database` or `file`. No Redis, no Elasticsearch, no Node on production.
- Search: MySQL FULLTEXT (InnoDB). Everything behind a `SearchService` so Meilisearch can replace it later.
- Frontend assets are built in dev/CI and deployed as `public/build`. Never run `npm` on the production server.
- Database: `utf8mb4`, Unicode collation. All stored text is Unicode NFC-normalized. Support RTL (`dir` attribute) and set `lang="xx"` on every element that holds proverb text.

Before adding any Composer package, check it is maintained, supports the current Laravel, and is truly needed. Prefer Laravel built-ins.

## 3. Memory budget rules (2 GB server)

- Chunk everything: `chunkById`, `lazy()`, cursors. Never load a full table into memory.
- Importer and AI jobs run as queued jobs or artisan commands with `--memory` limits, never in web requests.
- One queue worker, `--max-time=3600 --memory=128`. Filament is used only by the owner.
- Public GET pages must be cacheable at the web-server layer (see section 12). Laravel is mainly for writes and cache misses.

## 4. Domain-agnostic rules (hard requirement)

The domain, site name and contact must be changeable **only** in `.env`.

- Allowed config: `APP_URL`, `SITE_NAME`, `SITE_TAGLINE`, `CONTACT_EMAIL`.
- Never hardcode a domain, site name or email in PHP, Blade, JS, CSS, migrations, seeders, factories (except `example.test`), Markdown pages, or the database.
- Always generate URLs with `route()`, `url()` or `config('app.url')`. Canonical tags, sitemaps, RSS, Open Graph, `robots.txt`, `ads.txt`, JSON-LD and emails derive from `APP_URL`.
- Stored content must not contain absolute URLs to our own site. Store slugs and paths, render absolute URLs at output time.
- Markdown pages in `resources/pages/` use placeholders `{{SITE_NAME}}`, `{{SITE_URL}}`, `{{CONTACT_EMAIL}}`, replaced at render time.
- `scripts/check-domain-agnostic.sh` must pass (CI and before every commit). It fails if the temporary domain literal appears anywhere except `.env.example`, `docs/` and the agent instruction files (`AGENTS.md`, `CLAUDE.md`).
- Domain-change procedure lives in `docs/DEPLOY.md` and must stay accurate when behavior changes.

## 5. Environment variables (all feature flags live here)

See `.env.example` for the full list. Key groups:

```
SITE_NAME, SITE_TAGLINE, APP_URL, CONTACT_EMAIL
SITE_INDEXABLE=false            # false: X-Robots-Tag noindex + robots.txt Disallow (temp domain)
AI_PRIMARY=groq  AI_FALLBACK=openrouter
GROQ_API_KEY, GROQ_MODEL, OPENROUTER_API_KEY, OPENROUTER_MODEL
AI_THROTTLE_SECONDS, AI_DAILY_LIMIT, AI_PROMPT_VERSION
ADSENSE_ENABLED=false  ADSENSE_CLIENT=
ANALYTICS_ENABLED=true GA4_MEASUREMENT_ID=
CMP_PROVIDER=quantcast CMP_ID=
ANALYTICS_ALLOW_WITHOUT_CMP=false   # dev only
TRUST_AFTER_APPROVED, SEO_MIN_ENRICHMENT_SCORE, WIKTIONARY_CONTACT (defaults to CONTACT_EMAIL)
```

Read env only inside `config/*.php`. Application code uses `config()`.

## 6. Data model (starting point; refine via migrations, keep names)

- `languages`: code (ISO 639), name, native_name, script, direction, enabled, wiktionary_category, import_rank.
- `countries`: iso2, name. Pivot `country_proverb`, `country_language`.
- `themes`: slug, name. Pivot `proverb_theme`.
- `ideas`: slug, title, summary, status. A proverb belongs to at most one idea.
- `proverbs`: language_id, slug (unique), text, text_normalized, text_hash (sha256 of normalized text; **unique with language_id**, this is the dedup key), literal_translation, meaning, usage_example, origin_note, idea_id (nullable), `status` (`draft|pending|approved|rejected|archived`), `source_type` (`wiktionary|user|ai|public_domain`), source_url, source_title, source_revision, license, attribution, enrichment_score, ai_confidence, published_at.
- `proverb_links`: proverb_id, related_proverb_id, `type` (`equivalent|close|opposite`), status, source (`ai|user|import`).
- `contributions`: proverb_id (nullable for new proverb), `type` (`new_proverb|translation|meaning|equivalent|usage|origin|remix`), payload JSON, player_id, status, ip_hash, license_ack (boolean, **must be true**), moderated_at.
- `ai_drafts`: proverb_id, provider, model, prompt_version, input_hash, output JSON, status (`queued|ok|invalid|failed|applied`), error, tokens. Never delete; this is the audit trail and allows bulk regeneration.
- `proverb_of_day`: date (unique), proverb_id.
- `players`: token_hash, nickname, recovery_code_hash, trusted (bool), approved_count.
- `quiz_runs`: player_id, quiz_type, run_date, seed, questions JSON (server side), answers JSON, score, duration_ms, completed_at.
- `reports`: reportable (morph), reason (`error|offensive|copyright|other`), details, ip_hash, status.

Indexes: FULLTEXT on `text`, `literal_translation`, `meaning` (separate index for CJK using the ngram parser, added later). Foreign keys everywhere. Soft deletes only where moderation needs undo.

## 7. Content lifecycle and visibility

Public visibility rules (enforce in one place: a `Proverb::scopePublic()` scope plus a policy):

1. Only `approved` proverbs are public. `draft`, `pending`, `rejected` return **404** (not noindex).
2. AI output never goes public by itself. It is written to `ai_drafts`, attached to the proverb as `draft`/`pending`, and applied to public fields only after the owner approves.
3. An approved proverb is **indexable** only if `enrichment_score >= SEO_MIN_ENRICHMENT_SCORE` (meaning present, theme set, at least one equivalent or usage example). Otherwise: page is visible, `<meta name="robots" content="noindex,follow">`, excluded from sitemaps.
4. Every imported entry stores source URL, revision and license and shows attribution on the page.

## 8. Importer (see `docs/agents/importer.md`)

- `php artisan proverbs:rank-languages` counts members of Wiktionary "`<Language> proverbs`" categories via the MediaWiki API, prints a ranked table, and writes nothing until the owner confirms. The confirmed top 10 go to `config/languages.php`. Adding a language later = config change + import command.
- `php artisan proverbs:import {lang}` is idempotent (dedup via `text_hash`), resumable, chunked, rate limited, and sends a descriptive User-Agent that includes `WIKTIONARY_CONTACT`. Respect Wikimedia API etiquette; prefer official dumps for bulk work.
- Imported entries land as `draft` with `source_type=wiktionary`. Never import the same entry twice; update revision metadata instead.
- Content license for imports and for all user contributions: **CC BY-SA** (verify the exact current version on Wiktionary and state it in `/licensing`). Attribution is shown on every entry.
- Optional extra source: public-domain proverb collections. Record the exact source and why it is public domain.

## 9. AI layer (see `docs/agents/ai-drafter.md`)

- Interface `App\Services\Ai\AiProvider` with drivers `GroqProvider` and `OpenRouterProvider`. `AiRouter` tries `AI_PRIMARY`, falls back to `AI_FALLBACK` on 429, 5xx, timeout or invalid output. Adding a provider must not touch calling code.
- All calls run in queued jobs. Throttle with `AI_THROTTLE_SECONDS` between calls and stop at `AI_DAILY_LIMIT`. Free tier limits change; they live in config, never in code. Implement a circuit breaker per provider.
- Prompts live in `resources/prompts/` and are versioned (`AI_PROMPT_VERSION`). Every `ai_drafts` row records provider, model and prompt version.
- Output must be strict JSON validated against a schema: `meaning`, `literal_translation`, `english_gloss`, `themes[]`, `idea_label`, `usage_example`, `origin_note` (null unless confident), `confidence` (0 to 1). Invalid JSON: one repair retry, then `invalid`.
- **Equivalents are selected, not invented.** Step 1 produces meaning, gloss, themes. Step 2 retrieves candidate proverbs from our own database (same theme or FULLTEXT on gloss) and the model only chooses among candidates by id, with a link type. If no candidate fits, no link is created.
- Never state a historical origin or attribute a proverb to a person/culture unless the model is confident; otherwise `origin_note = null`.
- Privacy: send only proverb text and language to providers. No user data, IPs or nicknames.
- Free models are weak on rare languages: low confidence stays `draft`, sorted last in the moderation queue.

## 10. Contributions and moderation (see `docs/agents/moderation.md`)

- Anonymous contributions are allowed. Identity is an anonymous token cookie (stored hashed in `players`).
- A contribution requires the checkbox **"I agree my contribution is published under CC BY-SA"**. Server rejects without it. Store `license_ack`.
- Spam defense on all public POST routes: honeypot, minimum-time trap, per-IP and per-token rate limits, link blocking (no URLs in contributions), length limits, optional AI spam/abuse classifier. Store only a **hashed** IP (HMAC with app key).
- Filament moderation queue: bulk approve/reject, sorted by AI confidence, diff view for edits, one-click "approve all high-confidence for this language".
- Trust: after `TRUST_AFTER_APPROVED` approved contributions a player becomes `trusted`; trusted players are auto-approved except for link-containing or flagged text. Owner can revoke trust.
- "Report this entry" on every proverb page (error, offensive, copyright, other) feeds a Filament report queue.
- Sanitize and escape all user text on output. No HTML allowed in contributions.

## 11. Public pages and routes

```
/                         home: proverb of the day, search, featured ideas
/proverb/{slug}
/language/{code}   /country/{slug}   /theme/{slug}
/idea/{slug}              same thought in N languages
/day/{YYYY-MM-DD}         Proverb of the Day archive; /day/today redirects
/search?q=
/contribute               new proverb or build on an existing one
/quiz   /quiz/daily   /leaderboard
/about /privacy /cookies /terms /licensing /content-policy /contact
/sitemap.xml  /robots.txt  /ads.txt (only if ADSENSE_ENABLED)  /feed.xml
```

- **Proverb of the Day:** scheduler fills the next 365 days in advance from approved, indexable, enriched proverbs only; no repeats within the cycle; variety across languages.
- **Idea pages:** list proverbs by language, link type labels (equivalent, close, opposite), literal translations.
- **Quiz types:** guess the country, guess the language, complete the proverb, match the equivalent, pick the true meaning. Questions are built only from approved data. Distractors come from the same language/theme. Questions and correct answers are stored server side in `quiz_runs`; the client never receives the answer key. Score is computed on the server only.
- **Daily quiz:** deterministic seed per date, one attempt per player per day.
- **Leaderboard:** daily, weekly, all-time; cached; no emails or passwords.
- **Nicknames:** generated `Adjective + Animal + number` from curated lists in `resources/wordlists/` (family-friendly, reviewed). No free-text nicknames. A "reroll" button. Optional one-time **recovery code** (shown once, stored hashed) to move results to another device. The owner can rename or block a nickname in Filament.

## 12. Caching and cookies (important for the 2 GB server)

- Public GET routes must **not start a session or set cookies**. Use route groups without `StartSession`/`ShareErrorsFromSession`.
- Forms (contribute, report, contact, quiz) get their CSRF token and player token from a small uncached JSON endpoint, or live on uncached routes. Never embed a CSRF token in a page that is full-page cached.
- Cache public pages at the web-server layer (CloudPanel/Nginx cache or Varnish, whichever the server supports) and purge on approval/edit. Provide `php artisan site:purge-cache`.
- Sitemaps are generated by the scheduled command `php artisan sitemap:build` into files, split by type and language, max 10,000 URLs each, with an index.

## 13. Analytics, CMP, AdSense (flags in `.env`)

- **AdSense:** `ADSENSE_ENABLED=false` initially. A Blade component `<x-ad-slot name="..."/>` renders **nothing** when disabled (no empty boxes, no layout shift). The AdSense script and `/ads.txt` exist only when enabled. Slots: below proverb meaning, between idea groups, between quiz questions (verify AdSense placement policies before enabling). Never place ads on `noindex`, empty or error pages.
- **Analytics:** GA4 with Consent Mode v2, consent denied by default for EEA/UK. Events: `quiz_start`, `quiz_complete`, `contribution_submit`, `idea_view`, `proverb_of_day_view`, `report_submit`, `search`.
- **CMP:** `CMP_PROVIDER=quantcast` now (Quantcast Choice, TCF v2.2). Later switch to Google Privacy & messaging when AdSense is enabled. Everything is in one partial: `resources/views/partials/cmp.blade.php`. Follow the provider's official install snippet; do not hand-write TCF logic. If `CMP_PROVIDER` is empty, no banner.
- **Gating rule:** GA4 and AdSense scripts load only after consent. GA4 additionally requires a configured CMP (`CMP_ID` set) unless `ANALYTICS_ALLOW_WITHOUT_CMP=true` (local dev only). Result: until `CMP_ID` is set, GA4 does not fire for real visitors.
- CMP IDs and AdSense approval are tied to the domain. After a domain change they must be reconfigured (see `docs/DEPLOY.md`).

## 14. Legal and static pages

Markdown files in `resources/pages/`: `about`, `privacy`, `cookies`, `terms`, `licensing`, `content-policy`, plus `contact` (form with honeypot and rate limit, mail to `CONTACT_EMAIL`).

- Privacy text must match the real implementation: GA4, CMP, anonymous player cookie, hashed IPs for rate limiting, Groq/OpenRouter receive only proverb text, server logs, retention periods.
- Licensing page: CC BY-SA for Wiktionary-derived and user content, how to attribute, how to report copyright problems.
- These are templates. Mark them in `docs/DEPLOY.md` as "owner must review before launch". Do not claim they are legal advice.

## 15. SEO rules

- One H1 per page, descriptive titles, meta descriptions from real content, canonical from `APP_URL`.
- `lang` attribute on proverb text, `dir` for RTL.
- JSON-LD only where accurate (breadcrumbs, website search). No fake ratings, no fabricated schema.
- Internal links: proverb ↔ idea ↔ country ↔ language ↔ theme. Idea page lists all members.
- Never generate pages for empty combinations. No doorway pages.
- `SITE_INDEXABLE=false` sends `X-Robots-Tag: noindex` and a Disallow robots.txt (used on the temporary domain). The owner flips it when ready.

## 16. Security

- CSRF on all state changes, validation via Form Requests, authorization via policies, mass-assignment guarded.
- Filament panel behind strong auth (2FA enabled), path configurable via env, only the owner account exists.
- Rate limiting on every public POST and on quiz submit. Quiz scoring server-side only.
- Secrets only in `.env`. Never log API keys, tokens or raw IPs.
- Escape everything; no raw HTML from users or from AI output.
- Security headers (CSP appropriate for GA/CMP/AdSense when enabled, `X-Content-Type-Options`, `Referrer-Policy`).

## 17. Testing and definition of done

- Pest or PHPUnit. Every milestone ships feature tests for its rules (visibility, dedup, license_ack, rate limits, quiz scoring, domain-agnostic output).
- Required tests: draft/pending pages 404; thin pages noindex and absent from sitemaps; URLs in sitemap/canonical follow `APP_URL` when changed in a test; ad slot renders nothing when disabled; GA4 not rendered without CMP; contribution without `license_ack` fails; importer idempotency; AI JSON schema validation and fallback routing (with HTTP fakes, never real API calls in tests).
- Run before finishing any task: `composer test`, `vendor/bin/pint --test`, `scripts/check-domain-agnostic.sh`.
- A task is done only when tests pass and `docs/DEPLOY.md` is updated if deployment or env changed.

## 18. Build order

1. Scaffold: Laravel, Filament, env/config, `.env.example`, domain guard script, layout, legal page skeleton.
2. Data model, migrations, factories, Filament resources.
3. Public pages, search, SEO, sitemaps, caching rules.
4. Importer: `rank-languages`, `import`.
5. AI layer: providers, router, queue, draft review in Filament.
6. Contributions, moderation queue, reports, spam defense, trust.
7. Ideas graph, links, Proverb of the Day.
8. Quiz, nicknames, leaderboard.
9. Analytics, CMP, ad slots, legal pages finalized.
10. Hardening: tests, backups, deploy docs, load check on a 2 GB machine.

Work one step at a time. Finish with passing tests before starting the next. After each step, list what changed and what the owner must decide.

## 19. Roles (agents)

Roles are plain Markdown in `docs/agents/`. When a task matches a role, **read that file first and follow it**:

| Role | File | Owns |
|---|---|---|
| importer | `docs/agents/importer.md` | Wiktionary/public-domain import, language ranking |
| ai-drafter | `docs/agents/ai-drafter.md` | AI providers, prompts, drafts, queue |
| moderation | `docs/agents/moderation.md` | contributions, spam, trust, Filament queues, reports |
| seo | `docs/agents/seo.md` | indexability, sitemaps, caching, legal/static pages, analytics/CMP/ads |
| quiz | `docs/agents/quiz.md` | quiz, nicknames, leaderboard, Proverb of the Day |

Claude Code also has thin wrappers in `.claude/agents/`. In Cursor or OpenCode, say: "Act as the `<role>` role from `docs/agents/<role>.md`."
Roles never override this file. If a role file conflicts with this file, this file wins and the conflict is reported.

## 20. Never do

- Never hardcode the domain, site name or contact email.
- Never make AI output public without owner approval.
- Never invent equivalents, origins or attributions.
- Never import content without recording source, revision and license.
- Never call real AI or Wikimedia APIs from tests.
- Never run heavy jobs inside web requests or load whole tables into memory.
- Never enable AdSense or ads on thin, noindex or empty pages.
- Never send user data to AI providers.
- Never add a dependency or change the stack without asking the owner.
