<?php
/**
 * Pretty URLs for one product-category tree:
 * /find-your-store/ -> product category archive
 * /find-your-store/{child-slug}/ -> child category archive
 * Keep ALL other product categories using /product-category/...
 */

add_action('init', function () {
    // Base category
    add_rewrite_rule(
        '^find-your-store/?$',
        'index.php?product_cat=find-your-store',
        'top'
    );

    // One level subcategory: /find-your-store/cryphon-fc/
    add_rewrite_rule(
        '^find-your-store/([^/]+)/?$',
        'index.php?product_cat=$matches[1]',
        'top'
    );
}, 20);

/**
 * Redirect old URLs:
 * /product-category/find-your-store/... -> /find-your-store/...
 */
add_action('template_redirect', function () {
    if (is_tax('product_cat')) {
        $term = get_queried_object();
        if (!$term || is_wp_error($term)) return;

        // Check if current term is find-your-store OR a descendant of it
        $root = get_term_by('slug', 'find-your-store', 'product_cat');
        if (!$root || is_wp_error($root)) return;

        $is_root = ($term->term_id === $root->term_id);
        $is_child = term_is_ancestor_of($root->term_id, $term->term_id, 'product_cat');

        if ($is_root || $is_child) {
            // If user is on the old base
            $request = trim($_SERVER['REQUEST_URI'], '/');

            if (str_starts_with($request, 'product-category/find-your-store')) {
                // Build the new URL based on full hierarchy under find-your-store
                $path = [];

                // Walk parents up to root
                $current = $term;
                while ($current && !is_wp_error($current) && $current->term_id !== $root->term_id) {
                    $path[] = $current->slug;
                    $current = ($current->parent) ? get_term($current->parent, 'product_cat') : null;
                }

                $path = array_reverse($path);
                $new = home_url('/find-your-store/' . (empty($path) ? '' : implode('/', $path) . '/') );

                wp_safe_redirect($new, 301);
                exit;
            }
        }
    }
});
