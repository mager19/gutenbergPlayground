# Externado: Novedades de Gutenberg

Block theme prototype based on the Universidad Externado de Colombia website. It hosts an interactive presentation of recent WordPress editor features ("Novedades Gutenberg") so the editorial team can try each one and vote on the ones worth adopting.

> Demo content only: programs, news, people and figures are examples, not official data.

## Try it in your browser

[Open in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/mager19/gutenbergPlayground/main/playground/blueprint.json)

Playground runs a full, disposable WordPress in the browser. The link installs this theme, creates the demo content and opens the presentation already signed in as administrator. Nothing is saved when the tab is closed.

What does not work inside Playground, by design:

- Live vote totals shared across browsers (each Playground is its own site).
- The AI program finder uses the keyword fallback (no AI provider is configured).
- The Claude connector (MCP) needs a local or hosted site.

## What is inside

- `templates/page-novedades.html` and `patterns/novedades-*.php`: the presentation, one pattern per chapter.
- `inc/features.php`: the feature catalog (single source of truth for cards, votes and the summary).
- `blocks/`: custom dynamic blocks built on the Interactivity API (votes, summary, "Pruébalo tú", program finder).
- `inc/finder.php`: program finder REST endpoint (AI Client with keyword fallback).
- `inc/abilities.php`: site abilities exposed to AI agents through the Abilities API and the MCP Adapter plugin.
- `inc/icons.php`: custom icon collection for the Icon block.
- `bin/seed.php`: idempotent demo content (`wp eval-file wp-content/themes/externado/bin/seed.php --user=admin`).

## Requirements

WordPress 7.1 or later, PHP 8.1 or later.
