# Role: seo

Read `AGENTS.md` first. This file adds role detail; `AGENTS.md` wins on conflict.

## Mission
Make good pages discoverable, keep thin pages out of the index, keep the site fast on a 2 GB server, and own everything that must be correct for AdSense approval later: legal pages, analytics, consent, ad slots.

## Owns
Indexability logic, `SitemapBuilder` command, `robots.txt`/`ads.txt`/RSS routes, meta/canonical/JSON-LD partials, caching and purge command, `resources/pages/*.md`, `resources/views/partials/cmp.blade.php`, analytics partial, `<x-ad-slot>`, tests.

## Indexability
- `indexable = approved AND enrichment_score >= SEO_MIN_ENRICHMENT_SCORE`. Define `enrichment_score` in one class (meaning +1, theme +1, usage example +1, at least one equivalent link +1, origin note +1; tune later).
- Not indexable: `<meta name="robots" content="noindex,follow">`, excluded from sitemaps.
- `draft`/`pending`/`rejected` return 404.
- `SITE_INDEXABLE=false`: send `X-Robots-Tag: noindex` globally and `Disallow: /` in robots.txt.

## Sitemaps
Scheduled command writes files to `public/sitemaps/`, split by type (proverbs, ideas, languages, countries, themes, days) and by language for proverbs, max 10,000 URLs per file, plus an index. All absolute URLs derive from `APP_URL`. Test that changing `APP_URL` changes every URL.

## Caching
- Public GET routes: no session, no cookies.
- Full-page cache at the web-server layer; purge on approve/unpublish/edit via `php artisan site:purge-cache` (document the CloudPanel/Nginx mechanism in `docs/DEPLOY.md`).
- Forms obtain CSRF and player token from an uncached JSON endpoint.

## Legal and static pages
Markdown in `resources/pages/` with placeholders `{{SITE_NAME}}`, `{{SITE_URL}}`, `{{CONTACT_EMAIL}}`: about, privacy, cookies, terms, licensing, content-policy; contact is a form. The privacy page must list what the implementation really does (GA4, CMP, anonymous player cookie, hashed IPs, Groq/OpenRouter receiving only proverb text, retention). Mark all as templates the owner must review. Not legal advice.

## Analytics, CMP, ads
- GA4 with Consent Mode v2, consent denied by default for EEA/UK; events listed in `AGENTS.md` section 13.
- CMP in one partial, selected by `CMP_PROVIDER` (`quantcast` now; `google` when AdSense is enabled). Use the provider's official snippet; do not hand-roll TCF.
- GA4 and AdSense scripts load only after consent; GA4 also needs `CMP_ID` unless `ANALYTICS_ALLOW_WITHOUT_CMP=true` (dev only).
- `<x-ad-slot>` renders nothing when `ADSENSE_ENABLED=false`; no placeholder markup. Never render on noindex, empty or error pages. `/ads.txt` only when enabled.

## Content quality guard
No doorway pages, no pages for empty combinations, no fabricated structured data, meta descriptions from real content, one H1 per page, internal links proverb ↔ idea ↔ country ↔ language ↔ theme.

## Done when
Tests: thin page noindex and not in sitemap, 404 on drafts, `APP_URL` change propagates, ad slot empty when disabled, GA4 absent without CMP, `ads.txt` conditional. `composer test` and `scripts/check-domain-agnostic.sh` green.
