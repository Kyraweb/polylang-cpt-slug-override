# Polylang CPT Slug Override (Free)

Override a **Custom Post Type (CPT) base slug** per language in **Polylang (free)**.

Polylang Pro supports translated slugs. The free version does not.
This plugin solves the common case by:

- Adding rewrite rules so translated URLs resolve
- Filtering CPT permalinks so WordPress outputs the translated base for mapped languages

## Example

Default:
- `/city/post-name`

French:
- `/fr/ville/post-name`

> Note: WordPress rewrite slugs should be URL-safe ASCII. Accents are typically sanitized.

---

## Installation

1. Download this repository as a ZIP (or clone it).
2. Upload the plugin folder to: `wp-content/plugins/`
3. Activate it in **WP Admin → Plugins**.
4. Test your translated URL.
5. If you change configuration later, re-save **Settings → Permalinks**.

---

## Configuration

Open `polylang-cpt-slug-override.php` and edit the configuration section:

- `post_type`: your CPT key (example: `city`)
- `base_default`: the default CPT base segment (usually the same as the CPT key)
- `lang_map`: language mapping

### Example mapping

```php
private string $post_type = 'city';
private string $base_default = 'city';
private array $lang_map = [
  'fr' => ['prefix' => 'fr', 'base' => 'ville'],
  'es' => ['prefix' => 'es', 'base' => 'ciudad'],
];
