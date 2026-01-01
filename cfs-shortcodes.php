<?php

// Register shortcode for the find store flow.
function cfs_findstore_grid_shortcode() {
    ob_start();
    $parent_term = get_term_by('slug', 'find-your-store', 'product_cat');
    $subcategories = array();

    if ( $parent_term && ! is_wp_error( $parent_term ) ) {
        $args = array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'parent'     => $parent_term->term_id,
        );
        $terms = get_terms( $args );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                // Collect all child categories regardless of description (if that's intended)
                $subcategories[] = $term;
            }
        }
    }

    if ( ! empty( $subcategories ) ) {
        ?>
        <style>
            .cfs-flex-subcats {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 20px;
                margin: 0;
                padding: 0;
            }
            .cfs-flex-subcat-item {
               background: #FCFCFC;
                padding: 2.5rem;
                width: 23%;
                box-shadow: rgba(149, 157, 165, 0.16) 0px 8px 24px;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .cfs-flex-subcat-img {
                width: 16rem;
                height: 16rem;
                object-fit: cover;
                margin-bottom: 2rem;
            }
            .cfs-flex-subcat-title {
                font-weight: bold;
                font-size: 1.6rem;
                text-transform:uppercase;
                margin-bottom: 0.8rem;
                text-align: center;
            }
            .cfs-flex-subcat-desc {
                font-size:1.6rem;
                color: #666;
                margin-bottom:1rem;
                text-align: center;
            }
            .cfs-flex-viewstore-btn{
              background: #020202;
                padding:1.6rem;
                color:#fff !important;
                text-align:center;
                width:100%;
            }
        </style>
        <div class="cfs-flex-subcats">
        <?php
        foreach ( $subcategories as $subcat ) {
            // Get thumbnail ID and URL
            $thumb_id = get_term_meta($subcat->term_id, 'thumbnail_id', true );
            $img_url = $thumb_id ? wp_get_attachment_url( $thumb_id ) : '';
            // Get the category link and remove /product-category/ from the URL if present, but don't add find-your-store
            $category_link = get_term_link( $subcat );
            if ( ! is_wp_error( $category_link ) ) {
                $category_link = str_replace('/product-category/', '/', $category_link);
            }

            ?>
            <div class="cfs-flex-subcat-item">
                <?php if ( $img_url ) : ?>
                    <a href="<?php echo esc_url( $category_link ); ?>"> <img class="cfs-flex-subcat-img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($subcat->name); ?></a>
                <?php endif; ?>
                <a  href="<?php echo esc_url( $category_link ); ?>"> <div class="cfs-flex-subcat-title"><?php echo esc_html( $subcat->name ); ?></div></a>
                <div class="cfs-flex-subcat-desc"><?php echo esc_html( $subcat->description ); ?></div>
                <?php if ( ! is_wp_error( $category_link ) ): ?>
                    <a class="cfs-flex-viewstore-btn" href="<?php echo esc_url( $category_link ); ?>">View Store</a>
                <?php endif; ?>
            </div>
            <?php
        }
        echo '</div>';
    } else {
        echo '<p>No subcategories found under "find-your-store".</p>';
    }

    return ob_get_clean();
}
add_shortcode('cfs_findstore_grid', 'cfs_findstore_grid_shortcode');