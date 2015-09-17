<?php

include( 'classes/class.generic.php' );
include( 'classes/class.checklist.php' );
include( 'classes/class.client.php' );
include( 'classes/class.site.php' );



function dash_scripts() {

	wp_enqueue_script( 'global', get_template_directory_uri() . '/assets/js/global.js', array('jquery'), '1.0.0', true );
	wp_enqueue_script( 'jsencrypt', get_template_directory_uri() . '/lib/js/jsencrypt.js' );
	// wp_enqueue_script( 'material-design-lite', get_template_directory_uri() . '/node_modules/material-design-lite/dist/material.min.js' );

	wp_enqueue_style( 'style', get_stylesheet_uri() );
	// wp_enqueue_style( 'material-design-lite', get_template_directory_uri() . '/node_modules/material-design-lite/dist/material.min.css' );
	// wp_enqueue_style( 'material-design-iconfont', 'https://fonts.googleapis.com/icon?family=Material+Icons' );

}

add_action( 'wp_enqueue_scripts', 'dash_scripts' );

add_action('wp_head','dash_ajaxurl');

function dash_ajaxurl() {

	?><script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script><?php

}

add_action( 'init', function() {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( '/%postname%/' );
} );

add_action( 'wp_ajax_dash_new_post', 'ajax_dash_new_post' );

function ajax_dash_new_post() {

	$type = $_POST['type'];

	$name = $_POST['name'];

	$post_id = wp_insert_post(
		array(
			'post_title' => $name,
			'post_type' => $type,
			'post_status' => 'publish'
		)
	);

	echo get_the_permalink( $post_id );

	die();

}

function dash_concat_slug( $data ) {

	$slug = '';

	foreach( $data as $data_slug => $values ) {

		if( $values) $slug .= "_$data_slug";

		if( is_array( $values ) ) {

			$slug .= dash_concat_slug( $values );

		}

	}

	return $slug;

}

function dash_get_value( $data ) {

	foreach( $data as $slug => $value ) {

		if( is_array($value) || is_object($value) ) return dash_get_value($value);

		if( ! $value ) continue;

		return $value;

	}

}

function dash_array( $path, $value ) {

	$path = array_reverse($path);

	$return = $value;

	foreach( $path as $i => $key ) {

		if( $i == count($path) - 1) continue;

		$return = array($key => $return);

	}

	return $return;
}

add_action( 'wp_ajax_dash_update_field', 'ajax_dash_update_field' );

function ajax_dash_update_field() {

	$post_id = $_POST['post'];

	foreach( $_POST as $card => $data ) {

		if( $card == 'action' || $card == 'post' ) continue;

		$field = "_dash_" . $card . dash_concat_slug( $data );

		$value = dash_get_value( $data );

	}

	update_post_meta( $post_id, $field, $value );

	//DEBUG: global $wpdb; echo $wpdb->last_error.' '; echo $wpdb->last_query;


	die();
}

function unslugify( $slug ) {

	return ucwords( str_replace( "_", " ", $slug ) );
}

add_action( 'show_admin_bar', 'dash_check_admin_bar' );

function dash_check_admin_bar() {

	return false;

}

add_action( 'wp_ajax_dash_save_keys', 'ajax_dash_save_keys' );
function ajax_dash_save_keys() {

	echo print_r($_POST,true);

	update_option('dash_publickey', $_POST['publickey']);

	die();

}

function dash_set_keys_func( $atts ) {
	ob_start();

	?>
	<textarea id="publickey" placeholder="publickey"></textarea>
	<textarea id="privatekey" placeholder="privatekey"></textarea>
	<input type='submit' name="dash-keys" value="Save Keys"/>

	<?php


	return ob_get_clean();
}
add_shortcode( 'dash-setkeys','dash_set_keys_func' );

 ?>