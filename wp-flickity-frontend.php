<?php
/**
 * FRONTEND FLICKITY CSS
 * @since  0.1
 * @uses  cdn
 */
function wp_flickity_css_library() {
	wp_enqueue_style( 'wp-flickity-css', '//unpkg.com/flickity@2.0.4/dist/flickity.min.css', false,null );
	wp_enqueue_style( WP_FLICKITY_UNIQUE_IDENTIFIER.'wp-flickity-css-custom', WP_FLICKITY_PLUGIN_URL . 'assets/wp-flickity-custom-frontend.css', true, WP_FLICKITY_VERSION );
}
add_action( 'wp_enqueue_scripts', 'wp_flickity_css_library',1 );


/**
 * FRONTEND FLICKITY JS
 * @since  0.1
 * @uses  cdn
 */
function wp_flickity_script_library() {
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'wp-flickity-js', 'https://unpkg.com/flickity@2.0/dist/flickity.pkgd.min.js', false,null );
}
add_action( 'wp_enqueue_scripts', 'wp_flickity_script_library',100 );

//
?>