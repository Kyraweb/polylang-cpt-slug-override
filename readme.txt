=== Polylang CPT Slug Override (Free) ===
Contributors: Kyra Web Studio
Tags: polylang, custom post type, rewrite, permalink, multilingual
Requires at least: 5.8
Tested up to: 6.5
Stable tag: 1.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Override a Custom Post Type (CPT) base slug per language in Polylang (free).

== Description ==
Polylang Pro supports translated slugs. The free version does not.
This plugin adds rewrite rules and filters CPT permalinks to output a translated base slug by language.

Example:
Default: /city/post-name
French:  /fr/ville/post-name

== Installation ==
1. Upload to /wp-content/plugins/
2. Activate in Plugins
3. If you change configuration, go to Settings -> Permalinks and Save Changes

== Frequently Asked Questions ==
= Why do accented slugs sometimes fail? =
WordPress rewrite slugs should be URL-safe ASCII. Accents are usually sanitized.

= Does it redirect old URLs? =
No. This plugin focuses on resolving and generating correct translated URLs.

== Changelog ==
= 1.0.0 =
* Initial release
