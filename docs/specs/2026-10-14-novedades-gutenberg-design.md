# "Novedades Gutenberg" showcase — Design

Date: 2026-10-14
Status: Approved for implementation
Environment: local only (`http://demogutenbergexternado.local`, WordPress 7.1.3, theme `externado`)

## Goal

Present the latest Gutenberg features to the Externado site editors (semi-technical, already know Gutenberg) **inside WordPress**, so they can tell us which ones they find valuable. The page must demonstrate value, not just list features, and collect their votes.

## Audience and outcome

- Audience: Externado editorial team.
- Outcome: each editor votes "Me interesa / No por ahora" per feature; a summary turns votes into a value-vs-effort matrix that works as a prioritized roadmap.

## Feature catalog (16 items)

| ID | Feature | Effort |
|----|---------|--------|
| notes | Notes (block comments) | A – ready to use |
| revisions | Visual revisions | A |
| visibility | Block visibility / per-device | A |
| accordion | Accordion | A |
| tabs | Tabs | A |
| breadcrumbs | Breadcrumbs | A |
| playlist | Playlist | A |
| icon | Icon | A |
| zoom-out | Zoom Out | A |
| section-styles | Section styles | B – theme setup |
| query-grid | Grid responsive, Query Loop, Terms Query | B |
| style-book | Style Book | B |
| pattern-overrides | Pattern Overrides | C – development |
| block-bindings | Block Bindings | C |
| interactivity | Interactivity API | C |

The catalog lives in one PHP source of truth (`inc/features.php`) used by the vote block, the summary block and the REST endpoint.

## Narrative: "Abre el período de inscripciones"

Features are grouped into story chapters instead of a flat catalog:

1. **Preparar la campaña** — Zoom Out, section styles, Style Book, block CSS.
2. **Activar el anuncio** — block visibility (hidden banner, per-device variants), Icon.
3. **Revisar la convocatoria** — Notes, visual revisions.
4. **Publicar la ficha del programa** — Block Bindings, Pattern Overrides, Breadcrumbs, Tabs, Accordion.
5. **Difundir** — Query/Grid/Terms Query (news and faculties), Playlist (Externado Radio), Interactivity API.
6. **Resultado** — vote summary matrix.

Each feature unit shows: what it is, value for Externado, a live demo (front end) or step-by-step "try it in the editor" instructions (editor-only features), the "Pruébalo tú" button (opens the editor on the related demo content) and the vote control.

## Components

### Templates and patterns
- `templates/page-novedades.html` custom template ("Novedades Gutenberg").
- One pattern per chapter (`patterns/novedades-*.php`) plus a feature-unit pattern.
- The page itself uses Tabs, Accordion, Breadcrumbs and section styles, with a footer note stating it ("Esta página está hecha con lo que presenta").

### Section styles
- `styles/sections/*.json` (block style variations for Group/Columns): `section-verde`, `section-pizarra`, `section-gris`.

### Custom blocks (no build step)
- Blocks registered from `blocks/*/block.json`, dynamic (PHP `render.php`), front end via `viewScriptModule` importing `@wordpress/interactivity`. Editor script uses `wp.*` globals and `ServerSideRender`.
- `externado/feature-vote` — attribute `feature` (catalog ID). Shows two buttons; current user's vote highlighted; saves via REST without reload; logged-out users see a "Inicie sesión para votar" link.
- `externado/vote-summary` — value-vs-effort matrix: rows = effort A/B/C, columns = interest (votes "Me interesa" vs "No por ahora"), per-feature counts; also a ranked list.

### Votes storage and REST
- Stored as user meta `externado_votes` (array feature_id => `yes|no`). Summary aggregates across users.
- `POST /externado/v1/vote` `{feature, vote}` — requires logged-in user (`read` capability) and REST nonce; validates feature ID against the catalog and vote against `yes|no|clear`.
- `GET /externado/v1/votes` — aggregated counts (logged-in only).

### Block Bindings / Pattern Overrides demo
- CPT `programa` (public, `show_in_rest`, editor with notes enabled) with meta `duracion`, `modalidad`, `titulo_otorgado`, `snies` registered via `register_post_meta`.
- `templates/single-programa.html` binds paragraphs to those meta keys (`core/post-meta`).
- Synced pattern "Tarjeta docente" and "CTA Inscripciones" with overrides (`core/pattern-overrides`).

### Demo content (WP-CLI, idempotent)
- `bin/seed.php` run with `wp eval-file`: creates/updates the Novedades page (template assigned), the Derecho programa with meta, a "Convocatoria" page with a note, sample news posts and categories (faculties), synced patterns. Lookups by slug so re-running does not duplicate.
- "Pruébalo tú" links point to `post.php?post=<id>&action=edit` resolved at render time by slug.

## Out of scope
- Deployment outside local; before/after screenshots; real program data.

## Change log
- Demo mode (requested during review): visitors without a session can vote with an anonymous per-browser token (localStorage UUID, `X-Externado-Voter` header, stored in the `externado_anon_votes` option, max 500 voters). Results are public and every page polls `GET /externado/v1/votes` every 3 s, so totals update live across browsers.

## Verification
- PHP lint on all PHP files; JSON validity of theme.json/block.json/styles.
- Seed runs twice without duplicates.
- Front end of the Novedades page renders all chapters; voting works logged in (persisted after reload); summary reflects votes; logged-out sees login link.
