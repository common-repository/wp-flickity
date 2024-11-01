<?php
/**
 * Make the delta of db for upgrade checks
 * @return silently 
 * @since 0.1 
 */
function wp_flickity_delta_db(){
	global $installed_ver,$wp_flickity_db_version,$wp_flickity_db_structure;
	if ( $installed_ver != $wp_flickity_db_version ) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $wp_flickity_db_structure );
		update_option( "wp_flickity_db_version", $wp_flickity_db_version );
	}
}


/**
 * Checking the db if changed on plugin update (option)
 * @since 0.1 
 */
function wp_flickity_check_db() {
    global $wp_flickity_db_version;
    if ( get_site_option( 'wp_flickity_db_version' ) != $wp_flickity_db_version ) {
        wp_flickity_install();
    }
}
add_action( 'plugins_loaded', 'wp_flickity_check_db' );


/**
 * WP FLICKITY INSTALLATION 
 * @since  0.1
 * @uses   register_activation_hook,dbDelta
 */
function wp_flickity_install() {
	global 	$wpdb,
			$wp_flickity_db_structure,
			$wp_flickity_db_version,
			$wp_flickity_table_name,
			$flickity_db_charset_collate;

	$sql = $wp_flickity_db_structure;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'wp_flickity_db_version', $wp_flickity_db_version );
}
register_activation_hook( WP_FLICKITY_PLUGIN_FILE, 'wp_flickity_install' );


/**
 * WP FLICKITY AJAX BACKEND
 * @since  0.1
 * @uses  ajax,json,POST,wp_get_attachment_image_src,wp_die
 */
function wp_flickity_ajax_callback() {
	$images_ids = $_POST['imagesids'];
	$response = array();
	foreach ($images_ids as $id) {
			$response[] = array(
			'id'=>intval($id),
			'full' => wp_get_attachment_image_src($id,'full'),
			'thumbnail' => wp_get_attachment_image_src($id,'thumbnail')
			);
	}
	$response = json_encode($response,JSON_PRETTY_PRINT);

	// response output
	header( "Content-Type: application/json" );
	echo $response;

	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_flickity_ajax', 'wp_flickity_ajax_callback' );


function wp_flickity_edit_link($flickity_id=0){
	return admin_url( 'admin.php?page=wp-flickity&wp-flickity-page=edit&flickity-id='.$flickity_id);
}
require_once WP_FLICKITY_PLUGIN_PATH . 'wp-flickity-shortcodes.php';


function wp_ajax_flickity_preview(){
	header( "Content-Type: text/html" );
	ob_start();
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
            <?php wp_head();?>
			<link rel="stylesheet" type="text/css" href="<?=WP_FLICKITY_PLUGIN_URL . 'assets/wp-flickity-admin-preview.css'?>">
		</head>
		<body>
			<?=do_shortcode('[wp-flickity id="'.$_GET['flickity-id'].'"]' );?>
			<?php //wp_footer(); ?>
		</body>
	</html>

	<?php echo ob_get_clean(); wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_flickity_preview', 'wp_ajax_flickity_preview' );
add_action( 'wp_ajax_nopriv_flickity_previewr', 'wp_ajax_flickity_preview' );

function wp_flickity_preview_url($flickity_id=0){
	return admin_url( 'admin-ajax.php?action=flickity_preview&flickity-id='.$flickity_id);
}

//END OF FILE HERE