# Privacy

{{SITE_NAME}} processes limited data to operate the site.

## What we store
- Anonymous player token (cookie, stored hashed) for contributions and quiz results.
- Hashed IP addresses (HMAC with the application key) for rate limiting and spam defense only.
- Contribution text, nickname and quiz answers linked to the anonymous token.
- Server logs (standard access and error logs).

## Analytics and consent
Google Analytics 4 runs only after consent via the configured CMP. Consent Mode v2 is used; consent is denied by default for EEA/UK visitors until the banner is accepted.

## AI providers
When generating drafts, only the proverb text and language code are sent to Groq or OpenRouter. No user identifiers, nicknames or IP addresses are included.

## Cookies
- Essential: anonymous player token (when you contribute or take a quiz).
- Analytics: GA4 only after consent.
- Consent: CMP cookie set by the configured provider.

## Retention
- Player tokens and quiz runs: retained while the account remains active.
- Rate-limit hashes: short-lived.
- Server logs: standard system retention.

## Contact
Questions: {{CONTACT_EMAIL}}

*Template — owner must review and match to the real implementation before launch. Not legal advice.*
