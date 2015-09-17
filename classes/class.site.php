<?php


function cp_register_site() {

	$labels = array(
		'name'                => __( 'Sites', 'cp' ),
		'singular_name'       => __( 'Site', 'cp' ),
		'add_new'             => _x( 'Add New Site', 'cp', 'cp' ),
		'add_new_item'        => __( 'Add New Site', 'cp' ),
		'edit_item'           => __( 'Edit Site', 'cp' ),
		'new_item'            => __( 'New Site', 'cp' ),
		'view_item'           => __( 'View Site', 'cp' ),
		'search_items'        => __( 'Search Sites', 'cp' ),
		'not_found'           => __( 'No Sites found', 'cp' ),
		'not_found_in_trash'  => __( 'No Sites found in Trash', 'cp' ),
		'parent_item_colon'   => __( 'Parent Site:', 'cp' ),
		'menu_name'           => __( 'Sites', 'cp' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => true,
		'description'         => 'Website',
		'taxonomies'          => array('services-offered'),
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
			'title', 'editor', 'custom-fields', 'comments',	'revisions'
			)
	);

	register_post_type( 'site', $args );
}

add_action( 'init', 'cp_register_site' );


class SiteFactory {

	public static function create( $id ) {

		return new Site( $id );

	}

	public static function select( $return_type = 'id' ) {

		return GenericCPObject::select( 'site', $return_type );

	}

}

class Site extends GenericCPObject {

	use checklists;

	function __construct( $id ) {

		$this->cards['basic'] = array(
			'name' => 'Basic Information',
			'fields' => array(
				'billing-cycle' => array(
					'name' => 'Campaign Cycle',
					'type' => 'select',
					'options' => array(
						'0' => 'none',
						'1' => '1st of Month',
						'15' => '15th of Month'
					)
				),
				'organization-name' => FieldFactory::schema('text', 'Name of Organization Running Site'),
				'website-url' => array(
					'name' => 'Website URL',
					'desc' => 'url of website (include http(s) & (non-)www',
					'type' => 'url',
					'protocols' => array( 'http', 'https' )
				),
				'client' => array(
					'name' => 'Associated Client',
					'type' => 'select',
					'options' => ClientFactory::select()
				),
				'primary-contact' => array(
					'name' => 'Primary Contact',
					'type' => 'select',
					'options' => $this->Client->people
				),
				'project-manager' => array(
					'name' => 'Project Manager',
					'type' => 'select',
					'options' => $this->Client->people
				),
				'billing-contact' => array(
					'name' => 'Billing Contact',
					'type' => 'select',
					'options' => $this->Client->people
				),
				'naics' => FieldFactory::schema('text', 'NAICS Code'),
				'addresses' => array(
					'type' => 'array',
					'desc' => 'Addresses used by this site',
					'add-label' => 'Add Street Address',
					'options' => array(
						'street-address' => FieldFactory::schema('text', 'Street Address'),
						'notes' => FieldFactory::schema('textarea', 'Notes'),
					)
				),
				'phone-numbers' => array(
					'type' => 'array',
					'desc' => 'Phone numbers used by this site',
					'add-label' => 'Add Number',
					'options' => array(
						'phone-number' => FieldFactory::schema('tel', 'Phone Number'),
						'notes' => FieldFactory::schema('textarea', 'Notes'),
					)
				),
				'email-addresses' => array(
					'type' => 'array',
					'desc' => 'Email addresses used by this site',
					'add-label' => 'Add Email Address',
					'options' => array(
						'email-address' => FieldFactory::schema('email', 'Email Address'),
						'notes' => FieldFactory::schema('textarea', 'Notes'),
					)
				)
			)
		);



		$this->cards['logins'] = array(
			'fields' => array(
				'login' => array(
					'type' => 'array',
					'add-label' => 'Add Login',
					'options' => array(
						'type' => array(
							'type' => 'select',
							'options' => GenericCPObject::$login_types
						),
						'url' => FieldFactory::schema('text', 'Site URL'),
						'credential' => array(
							'type' => 'array',
							'add-label' => 'Add Credential',
							'options' => FieldFactory::schema('credential','Credential')
						),
					)
				)
			)
		);

		$this->cards['styles'] = array(
			'fields' => array(
				'color' => array(
					'type' => 'array',
					'add-label' => 'Add Color',
					'options' => array(
						'hex' => FieldFactory::schema('color', 'Hex Code')
					)
				),
				'font' => array(
					'type' => 'array',
					'add-label' => 'Add Font',
					'options' => array(
						'name' => FieldFactory::schema('text', 'Font Name')
					)
				)
			)
		);


		$this->cards['checklists'] = array(
			'name' => 'Checklists',
			'renderfunc' => array('checklists','render')
		);


		parent::__construct( $id );

	}
}

 ?>