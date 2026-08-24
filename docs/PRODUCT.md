# Product decisions (v0.1)

Scaffolded as a Cloud Agent session without an interactive Q&A. Defaults follow the OT plugin skill and can be changed later.

## Required

1. **FTP** — yes, shared rewriter/ot-seo account (`OT_PLUGINS_FTP_*`), remote dir `ot-hello` under `public_html/updates/plugins/`.
2. **GitHub** — https://github.com/hikikomorime/ot-hello. Update catalog slug is `ot-hello` (same pattern as OT re:Writer → `/updates/plugins/ot-rewriter/`).
3. **Local tree** — `C:\Users\micha\OneDrive\Dokumenty\AI-Projects\OneThird\ot-hello` (sibling of `ot-seo` / `ai-rewriter`).

## Standard set

| ID | Choice |
|----|--------|
| **A** Identity | Product **OT Hello**, menu **Hello**, slug/text domain `ot-hello`, PHP `OTHello\`, REST `ot-hello/v1`, license prefix `OT-HELLO` |
| **B** License | **license = updates** (re:Writer / Cache). Greeting and REST are not feature-gated. |
| **C** Admin | React Fluent SPA (Ads / SEO / Vercel family), not PHP Mission Control |
| **D** Sibling | Greenfield DI + React like `ot-seo` / `ot-security` / `ot-vercel`. Lives in the OneThird tree next to rewriter. |
| **M** MVP | Dashboard greeting, Settings save/load, Help, About + license field, `[ot_hello]` shortcode, updater, EN + `pl_PL` |

## Conditional (not asked)

- Audience: developers evaluating the OT line
- i18n: English source + `pl_PL` only
- Multisite / Agency / CallHome: out of v0.1
- Floors: WP 6.4, PHP 8.1
- CallHome is infrastructure only — never shown as a product feature
