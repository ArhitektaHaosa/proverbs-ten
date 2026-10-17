# Role: ai-drafter

Read `AGENTS.md` first. This file adds role detail; `AGENTS.md` wins on conflict.

## Mission
Produce first-draft meaning, literal translation, themes, usage example and equivalent links for proverbs using free hosted open models (Groq primary, OpenRouter fallback), cheaply and safely. A human approves everything.

## Owns
`app/Services/Ai/*` (`AiProvider`, `GroqProvider`, `OpenRouterProvider`, `AiRouter`, `DraftSchema`), `app/Jobs/GenerateDraftJob.php`, `config/ai.php`, `resources/prompts/*`, tests.

## Architecture
- `AiProvider` interface: `complete(array $messages, array $options): AiResult`. Drivers are interchangeable.
- `AiRouter`: tries `AI_PRIMARY`; on 429, 5xx, timeout or invalid JSON falls back to `AI_FALLBACK`. Per-provider circuit breaker (open after N consecutive failures, half-open after a cooldown).
- Queue worker pulls `GenerateDraftJob`. Throttle with `AI_THROTTLE_SECONDS`; stop at `AI_DAILY_LIMIT` (counter in cache/DB, resets at UTC midnight). Limits are config, never constants.
- Every call writes an `ai_drafts` row: provider, model, `prompt_version`, `input_hash`, output JSON, status, error, tokens.

## Two-step generation
1. **Enrich:** input = proverb text + language. Output JSON: `meaning`, `literal_translation`, `english_gloss`, `themes[]` (from a fixed theme list in config), `idea_label`, `usage_example`, `origin_note` (null unless confident), `confidence` 0..1.
2. **Link:** retrieve up to K candidates from our DB (same theme and/or FULLTEXT on `english_gloss`), excluding the same language. The model returns only `{candidate_id, type}` pairs, `type` in `equivalent|close|opposite`. **Never create a link to an id that was not in the candidate list. If nothing fits, return an empty list.**

## Rules
- Strict JSON, validated against `DraftSchema`. On invalid output: one repair retry, then mark `invalid`.
- Treat provider output as untrusted text: strip HTML, trim length, no URLs.
- Do not invent origins or attribute proverbs to people, cultures or eras. `origin_note` stays null when unsure.
- Be honest about uncertainty: low confidence for rare languages, unclear idioms, or ambiguous meaning.
- Privacy: send only proverb text and language. No users, IPs, nicknames.
- Applying a draft to public fields happens **only** after owner approval in Filament (`ai_drafts.status` becomes `applied`). The AI role never sets `approved`.
- Regeneration: a command `proverbs:regenerate --provider= --model= --prompt-version= [--lang=]` re-queues entries produced by a bad model or old prompt, keeping history.
- Prompts are files in `resources/prompts/`, versioned. Changing a prompt bumps `AI_PROMPT_VERSION`.
- Tests use HTTP fakes only. Cover: fallback routing, daily limit, invalid JSON, candidate-id enforcement, circuit breaker.

## Done when
Router and both drivers work against fakes, drafts appear in a Filament review screen sorted by confidence, nothing public changes without approval, `composer test` green. Report which free models/limits were assumed so the owner can verify them.
