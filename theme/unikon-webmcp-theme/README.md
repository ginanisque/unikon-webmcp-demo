# Unikon WebMCP Studio theme

> Legacy reference: this optional WordPress theme is retained for the earlier plugin version and is not used by the standalone Vercel deployment.

A minimal companion block theme for the Unikon WebMCP Fashion eSchool Demo plugin.

Version 0.3.0 focuses the new public landing page without adding a duplicate outer title.

## Behaviour

When activated by an administrator, the theme looks for the published page with the slug `fashion-learning-studio-home` and sets it as the static homepage. If the plugin has not created that page yet, an admin notice appears and the theme retries after the plugin is activated. Version 0.3.0 also migrates installations whose homepage is still the former `fashion-learning-studio` course page. Once configured, it does not repeatedly override later Reading Settings changes.

The front-page template intentionally omits the standard post title because the learning interface supplies its own primary heading. Other pages retain normal titles and content.

## Install

1. Install and activate the Unikon WebMCP Fashion eSchool Demo plugin.
2. Upload this theme ZIP through Appearance → Themes → Add New → Upload Theme.
3. Activate **Unikon WebMCP Studio**.
4. Visit the site homepage and purge SiteGround cache if necessary.

The theme contains no assets or source copied from another theme. It uses an independently implemented palette and layout suitable for this demonstration.
