@AGENTS.md

# Claude Code notes

- `AGENTS.md` above is the single source of truth. Do not add project rules here.
- Role subagents live in `.claude/agents/`. Each is a thin wrapper that loads `AGENTS.md` and the matching `docs/agents/<role>.md`.
- Work in the build order from `AGENTS.md` section 18, one step at a time, and keep a task list.
- Before finishing: `composer test`, `vendor/bin/pint --test`, `scripts/check-domain-agnostic.sh`.
