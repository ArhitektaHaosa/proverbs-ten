# Role: quiz

Read `AGENTS.md` first. This file adds role detail; `AGENTS.md` wins on conflict.

## Mission
Build the daily-return loop: Proverb of the Day, Daily Quiz, leaderboard, and anonymous nickname identity, all cheap to serve on a 2 GB server and impossible to cheat from the browser.

## Owns
`app/Services/Quiz/*`, quiz controllers/views, `app/Services/Players/NicknameGenerator.php`, `resources/wordlists/*`, leaderboard queries and cache, `ProverbOfDay` scheduler command, tests.

## Quiz types
1. Guess the country of a proverb.
2. Guess the language.
3. Complete the proverb (multiple choice, distractors from the same language/theme).
4. Match the equivalent (pick the proverb from another language that carries the same Idea).
5. Pick the true meaning (one true meaning, others plausible but wrong, drawn from other proverbs).

Questions are built **only from approved data**; no new moderation needed. A question type is skipped when there is not enough data for it.

## Anti-cheat and integrity
- On run start the server builds the question list and the answer key, stores them in `quiz_runs`, and sends the client only question text and options.
- Score is computed on the server on submit. Time is measured server side (start timestamp and submit timestamp), not trusted from the client.
- Daily quiz: seed derived from the date; one attempt per player per day; rate limit on start and submit.
- Leaderboards: daily, weekly, all-time; cached; ties broken by time.

## Nicknames
- Generated as `Adjective + Animal + number` from curated lists in `resources/wordlists/` (family-friendly, reviewed, no real-person names). No free-text input.
- Reroll button. Optional one-time recovery code, shown once, stored hashed, used to move results to another device.
- Owner can rename or block in Filament (moderation role owns the screen).

## Proverb of the Day
- Scheduled command fills the next 365 days into `proverb_of_day` with approved, **indexable**, enriched proverbs only; no repeats within the cycle; spread across languages; the owner can pin a specific day in Filament.
- `/day/{date}` is an indexable archive page; `/day/today` redirects.

## Performance
- Quiz pages are uncached but light; leaderboard queries are indexed and cached for 1 to 5 minutes.
- No session on public pages; player token only when a quiz or contribution starts.

## Analytics
Emit `quiz_start`, `quiz_complete`, `proverb_of_day_view` through the shared analytics partial (consent-gated, owned by the seo role).

## Done when
Tests: key never present in responses, server-side scoring, one daily attempt, deterministic daily seed, nickname generator produces unique valid names, recovery code works once, proverb-of-day never selects non-indexable entries. `composer test` green.
