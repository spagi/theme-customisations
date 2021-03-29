<?php
/**
 * Functions.php
 *
 * @package  Theme_Customisations
 * @author   WooThemes
 * @since    1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * functions.php
 * Add PHP snippets here
 */
add_action('woocommerce_after_add_to_cart_quantity', 'horak_after_add_to_cart_quantity');

function horak_after_add_to_cart_quantity()
{
    echo "Kg &nbsp;";
}

add_filter('woocommerce_get_price_suffix', 'horak_get_price_suffix');

function horak_get_price_suffix()
{
    return "/Kg";
}

add_filter('woocommerce_billing_fields', 'horak_billing_fields');

function horak_billing_fields($fields = [])
{
    unset($fields['billing_company']);
    unset($fields['billing_address_1']);
    unset($fields['billing_address_2']);
    unset($fields['billing_state']);
    unset($fields['billing_city']);
    unset($fields['billing_postcode']);
    unset($fields['billing_country']);

    return $fields;
}

add_filter('woocommerce_checkout_fields', 'horak_checkout_fields');

function horak_checkout_fields($fields)
{
    unset($fields['shipping']);

  // $fields['shipping']['shipping_city'] = [
  //     'type'     => 'select',
  //     'label'    => __('Město'),
  //     'required' => true,
  //     'class'    => [''],
  //     'options'  => [
  //         'Olomouc' => 'Olomouc',
  //         'Ostrava' => 'Ostrava',
  //     ],
  // ];

    return $fields;
}

function storefront_homepage_content()
{

    $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
    ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="<?php horak_homepage_content_styles(); ?>"
         data-featured-image="<?php echo esc_url($featured_image); ?>">
        <div class="col-full">
            <?php
            /**
             * Functions hooked in to storefront_page add_action
             *
             * @hooked storefront_homepage_header      - 10
             * @hooked storefront_page_content         - 20
             */
            do_action('storefront_homepage');
            ?>
        </div>
    </div><!-- #post-## -->
    <?php
}

function horak_homepage_content_styles()
{
    $featured_image   = get_the_post_thumbnail_url( get_the_ID() );
    $background_image = '';

    if ( $featured_image ) {
        $background_image = 'url(' . esc_url( $featured_image ) . ')';
    }

    $styles = array();

    if ( '' !== $background_image ) {
        $styles['background-image'] = $background_image;
    }
    $styles['min-height']="500px;";
    $styles = apply_filters( 'storefront_homepage_content_styles', $styles );

    foreach ( $styles as $style => $value ) {
        echo esc_attr( $style . ': ' . $value . '; ' );
    }
}

function storefront_homepage_header()
{
    edit_post_link(__('Edit this section', 'storefront'), '', '', '', 'button storefront-hero__button-edit');
    $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
    ?>
    <header class="entry-header" >

    </header><!-- .entry-header -->
    <?php
}


if ( ! function_exists( 'storefront_best_selling_products' ) ) {
    /**
     * Display Best Selling Products
     * Hooked into the `homepage` action in the homepage template
     *
     * @since 2.0.0
     * @param array $args the product section args.
     * @return void
     */
    function storefront_best_selling_products( $args ) {

    }
}


if ( ! function_exists( 'storefront_recent_products' ) ) {
    /**
     * Display Recent Products
     * Hooked into the `homepage` action in the homepage template
     *
     * @since  1.0.0
     * @param array $args the product section args.
     * @return void
     */
    function storefront_recent_products( $args ) {
        $args = apply_filters(
            'storefront_recent_products_args',
            array(
                'limit'   => 4,
                'columns' => 4,
                'orderby' => 'date',
                'order'   => 'desc',
                'title'   => __( 'Doporučujeme', 'storefront' ),
            )
        );

        $shortcode_content = storefront_do_shortcode(
            'products',
            apply_filters(
                'storefront_recent_products_shortcode_args',
                array(
                    'orderby'  => esc_attr( $args['orderby'] ),
                    'order'    => esc_attr( $args['order'] ),
                    'per_page' => intval( $args['limit'] ),
                    'columns'  => intval( $args['columns'] ),
                )
            )
        );

        /**
         * Only display the section if the shortcode returns products
         */
        if ( false !== strpos( $shortcode_content, 'product' ) ) {
            echo '<section class="storefront-product-section storefront-recent-products" aria-label="' . esc_attr__( 'Recent Products', 'storefront' ) . '">';

            do_action( 'storefront_homepage_before_recent_products' );

            echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

            do_action( 'storefront_homepage_after_recent_products_title' );

            echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            do_action( 'storefront_homepage_after_recent_products' );

            echo '</section>';
        }
    }
}

add_filter( 'manage_edit-shop_order_columns', 'horak_add_new_order_admin_list_column' );

function horak_add_new_order_admin_list_column( $columns ) {
    $columns['horak_shipping_city'] = 'Místo';
    unset($columns['billing_address']);
    return $columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'horak_add_new_order_admin_list_column_content' );

function horak_add_new_order_admin_list_column_content( $column ) {

    global $post;

    if ( 'horak_shipping_city' === $column ) {

        $order = wc_get_order( $post->ID );
        echo $order->get_shipping_method();

    }
}