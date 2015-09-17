<?php

/**
* Registers a new post type
* @uses $wp_post_types Inserts new post type object into the list
*
* @param string  Post type key, must not exceed 20 characters
* @param array|string  See optional args description above.
* @return object|WP_Error the registered post type object, or an error object
*/
function cp_register_client() {

	$labels = array(
		'name'                => __( 'Clients', 'cp' ),
		'singular_name'       => __( 'Client', 'cp' ),
		'add_new'             => _x( 'Add New Client', 'cp', 'cp' ),
		'add_new_item'        => __( 'Add New Client', 'cp' ),
		'edit_item'           => __( 'Edit Client', 'cp' ),
		'new_item'            => __( 'New Client', 'cp' ),
		'view_item'           => __( 'View Client', 'cp' ),
		'search_items'        => __( 'Search Clients', 'cp' ),
		'not_found'           => __( 'No Clients found', 'cp' ),
		'not_found_in_trash'  => __( 'No Clients found in Trash', 'cp' ),
		'parent_item_colon'   => __( 'Parent Client:', 'cp' ),
		'menu_name'           => __( 'Clients', 'cp' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'description'         => 'Client',
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array(
			'title', 'editor', 'custom-fields', 'comments', 'revisions'
			)
	);

	register_post_type( 'client', $args );
}

add_action( 'init', 'cp_register_client' );


class ClientFactory {

	public static function create( $id ) {

		return new Client( $id );

	}

	public static function select( $return_type = 'id' ) {

		return GenericCPObject::select( 'client', $return_type );

	}

}

class Client extends GenericCPObject {

	public function __construct( $post_id ) {

		$this->cards['basic'] = array(
			'name' => 'Basic Information',
			'fields' => array(
				'referral-source' => FieldFactory::schema('textarea', 'Referral Source'),
				'addresses' => array(
					'type' => 'array',
					'desc' => 'Addresses used by this client',
					'options' => array(
						'street-address' => FieldFactory::schema('text', 'Street Address'),
						'notes' => FieldFactory::schema('textarea', 'Notes'),
					)
				),
			)
		);

		$this->cards['people'] = array(
			'name' => 'People',
			'fields' => array(
				'people' => array(
					'type' => 'array',
					'options' => array(
						'name' => FieldFactory::schema('text', 'Name'),
						'street-address' => FieldFactory::schema('text', 'Street Address'),
						'email-address' => FieldFactory::schema('email', 'Email Address'),
						'phone-number' => FieldFactory::schema('tel', 'Phone Number'),
						'notes' => FieldFactory::schema('textarea', 'Notes'),
					)
				)
			)
		);

		parent::__construct( $post_id );

	}
}
