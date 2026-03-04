<?php
/**
 * Plugin Name: Polylang CPT Slug Override (Free)
 * Plugin URI:  https://github.com/Kyraweb/polylang-cpt-slug-override
 * Description: Override custom post type URL base slugs per language in Polylang (free) by adding rewrite rules + filtering permalinks.
 * Version:     1.0.0
 * Author:      Kyra Web Studio
 * Author URI:  https://kyraweb.ca
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: polylang-cpt-slug-override
 */

if (!defined('ABSPATH')) exit;

final class PLL_CPT_Slug_Override_Free {

    /**
     * ===========================
     * CONFIGURATION (EDIT THIS!)
     * ===========================
     *
     * Example goal:
     *   Default: /city/post-name
     *   French:  /fr/ville/post-name
     *
     * Notes:
     * - WordPress rewrite slugs should be URL-safe (ASCII). Accents are usually sanitized.
     * - 'prefix' should match your Polylang language URL format (e.g. /fr/).
     * - 'base' is your translated CPT base segment (e.g. 'ville').
     */

    // 1) Set your CPT key (post type slug)
    private string $post_type = 'city';

    // 2) Set your default CPT base segment that appears in URLs (often same as CPT key)
    private string $base_default = 'city';

    /**
     * 3) Map: language_code => [ 'prefix' => 'fr', 'base' => 'ville' ]
     *
     * If you want multiple languages:
     *   'fr' => ['prefix' => 'fr', 'base' => 'ville'],
     *   'es' => ['prefix' => 'es', 'base' => 'ciudad'],
     */
    private array $lang_map = [
        'fr' => ['prefix' => 'fr', 'base' => 'ville'],
    ];

    /**
     * 4) Optional: also add archive rewrite for translated base.
     * If you don't use CPT archives, set this false.
     */
    private bool $add_archive_rewrite = true;

    public function __construct() {
        add_action('init', [$this, 'add_rewrites'], 20);
        add_filter('post_type_link', [$this, 'filter_post_type_link'], 10, 2);

        register_activation_hook(__FILE__, [$this, 'on_activate']);
        register_deactivation_hook(__FILE__, [$this, 'on_deactivate']);

        add_action('admin_notices', [$this, 'admin_notice_activation']);
    }

    public function on_activate(): void {
        // Ensure rewrites exist before flushing
        $this->add_rewrites();
        flush_rewrite_rules();
        set_transient('pll_cpt_slug_override_activated', 1, 60);
    }

    public function on_deactivate(): void {
        flush_rewrite_rules();
    }

    public function admin_notice_activation(): void {
        if (!current_user_can('manage_options')) return;

        if (get_transient('pll_cpt_slug_override_activated')) {
            delete_transient('pll_cpt_slug_override_activated');
            echo '<div class="notice notice-success is-dismissible"><p><strong>Polylang CPT Slug Override:</strong> Rewrite rules flushed on activation. If you change configuration, re-save <em>Settings → Permalinks</em>.</p></div>';
        }
    }

    public function add_rewrites(): void {
        foreach ($this->lang_map as $lang => $cfg) {
            $prefix = trim((string)($cfg['prefix'] ?? ''), '/');
            $base   = trim((string)($cfg['base'] ?? ''), '/');

            if ($prefix === '' || $base === '') {
                continue;
            }

            /**
             * Single CPT post:
             * /{prefix}/{base}/{post-slug} => index.php?post_type={post_type}&name={post-slug}
             */
            add_rewrite_rule(
                '^' . preg_quote($prefix, '/') . '/' . preg_quote($base, '/') . '/([^/]+)/?$',
                'index.php?post_type=' . $this->post_type . '&name=$matches[1]',
                'top'
            );

            /**
             * Optional archive:
             * /{prefix}/{base}/ => index.php?post_type={post_type}
             */
            if ($this->add_archive_rewrite) {
                add_rewrite_rule(
                    '^' . preg_quote($prefix, '/') . '/' . preg_quote($base, '/') . '/?$',
                    'index.php?post_type=' . $this->post_type,
                    'top'
                );
            }
        }
    }

    public function filter_post_type_link(string $permalink, $post): string {
        if (empty($post) || empty($post->post_type) || $post->post_type !== $this->post_type) {
            return $permalink;
        }

        // Only modify permalinks if Polylang is active
        if (!function_exists('pll_get_post_language')) {
            return $permalink;
        }

        $lang = pll_get_post_language((int)$post->ID);
        if (!$lang || !isset($this->lang_map[$lang])) {
            return $permalink;
        }

        $cfg    = $this->lang_map[$lang];
        $prefix = trim((string)($cfg['prefix'] ?? ''), '/');
        $base   = trim((string)($cfg['base'] ?? ''), '/');

        if ($prefix === '' || $base === '') {
            return $permalink;
        }

        /**
         * Replace default base with translated base + prefix:
         * /{base_default}/ -> /{prefix}/{base}/
         */
        $permalink = str_replace(
            '/' . trim($this->base_default, '/') . '/',
            '/' . $prefix . '/' . $base . '/',
            $permalink
        );

        /**
         * Safety: avoid duplicate prefixes if something else already added it
         * e.g. /fr/fr/... => /fr/...
         */
        $permalink = str_replace('/' . $prefix . '/' . $prefix . '/', '/' . $prefix . '/', $permalink);

        return $permalink;
    }
}

new PLL_CPT_Slug_Override_Free();
