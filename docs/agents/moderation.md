# Role: moderation

Read `AGENTS.md` first. This file adds role detail; `AGENTS.md` wins on conflict.

## Mission
Let anonymous visitors build on proverbs while keeping a single human moderator fast and in control, and keeping spam and abuse out.

## Owns
Contribution form and controllers, `app/Services/Moderation/*`, Filament resources for contributions, AI drafts review, reports, players, spam defense middleware, trust logic, tests.

## Contribution types
`new_proverb`, `translation`, `meaning`, `equivalent`, `usage`, `origin`, `remix`. Stored in `contributions.payload` (JSON, validated per type). Equivalent contributions reference an existing proverb by id and a link type.

## Rules
1. **License gate:** the checkbox "I agree my contribution is published under CC BY-SA" is mandatory. Server-side validation rejects without it; store `license_ack=true`.
2. **Anonymous identity:** token cookie, stored hashed in `players`. No email, no password.
3. **Spam defense on every public POST** (contribute, report, contact, quiz submit): honeypot field, minimum-time trap, per-IP and per-token rate limits, no URLs allowed in contribution text, max lengths, duplicate detection (`text_hash`). Store only an HMAC-hashed IP.
4. **Optional AI classifier** for spam/abuse/hate: it can only raise the review priority or auto-reject obvious spam. It never approves anything.
5. **Trust:** a player becomes `trusted` after `TRUST_AFTER_APPROVED` approved contributions. Trusted contributions are auto-approved unless they contain a link-like string or trip a filter. Owner can revoke trust in Filament.
6. **Filament queues:**
   - Contributions: bulk approve/reject, filters by type/language/priority, diff view for edits to existing entries, approval writes to the proverb and refreshes `enrichment_score`.
   - AI drafts: sorted by confidence, side-by-side with current fields, "approve all above threshold for language X" with a confirmation step.
   - Reports: reason, entry link, resolve/dismiss, one-click unpublish.
   - Players: view nickname, rename or block, revoke trust.
7. **Output safety:** no HTML in user text. Escape everything. Reject content with obvious personal data.
8. Approval and unpublish purge the cached public page (`site:purge-cache` hook).
9. Audit trail: who/when moderated, previous values kept for undo.
10. A rejected or reported-and-removed entry returns 404 and leaves the sitemap on the next build.

## Done when
Tests cover: missing `license_ack` fails, honeypot and rate limits, URL blocking, trust promotion and revocation, approval updating `enrichment_score`, report workflow, 404 on unpublish. `composer test` green. Report what the owner must tune (thresholds, rate limits).
