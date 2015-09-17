<?php

/**
* Registers a new post type
* @uses $wp_post_types Inserts new post type object into the list
*
* @param string  Post type key, must not exceed 20 characters
* @param array|string  See optional args description above.
* @return object|WP_Error the registered post type object, or an error object
*/
function cp_register_checklist() {

	$labels = array(
		'name'                => __( 'Checklists', 'cp' ),
		'singular_name'       => __( 'Checklist', 'cp' ),
		'add_new'             => _x( 'Add New Checklist', 'cp', 'cp' ),
		'add_new_item'        => __( 'Add New Checklist', 'cp' ),
		'edit_item'           => __( 'Edit Checklist', 'cp' ),
		'new_item'            => __( 'New Checklist', 'cp' ),
		'view_item'           => __( 'View Checklist', 'cp' ),
		'search_items'        => __( 'Search Checklists', 'cp' ),
		'not_found'           => __( 'No Checklists found', 'cp' ),
		'not_found_in_trash'  => __( 'No Checklists found in Trash', 'cp' ),
		'parent_item_colon'   => __( 'Parent Checklist:', 'cp' ),
		'menu_name'           => __( 'Checklists', 'cp' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'description'         => 'Checklist',
		'taxonomies'          => array('checklist-type'),
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
			'title', 'custom-fields', 'comments',
			)
	);

	register_post_type( 'checklist', $args );
}

add_action( 'init', 'cp_register_checklist' );

function cp_register_checklist_type() {

	$labels = array(
		'name'                       => _x( 'Checklist Types', 'Taxonomy General Name', 'cp' ),
		'singular_name'              => _x( 'Checklist Type', 'Taxonomy Singular Name', 'cp' ),
		'menu_name'                  => __( 'Checklist Type', 'cp' ),
		'all_items'                  => __( 'All Checklist Types', 'cp' ),
		'parent_item'                => __( 'Parent Checklist Type', 'cp' ),
		'parent_item_colon'          => __( 'Parent Checklist Type:', 'cp' ),
		'new_item_name'              => __( 'New Checklist Type', 'cp' ),
		'add_new_item'               => __( 'Add Checklist Type', 'cp' ),
		'edit_item'                  => __( 'Edit Checklist Type', 'cp' ),
		'update_item'                => __( 'Update Checklist Type', 'cp' ),
		'view_item'                  => __( 'View Checklist Type', 'cp' ),
		'separate_items_with_commas' => __( 'Separate checklist types with commas', 'cp' ),
		'add_or_remove_items'        => __( 'Add or remove checklist types', 'cp' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'cp' ),
		'popular_items'              => __( 'Popular Checklist Types', 'cp' ),
		'search_items'               => __( 'Search Checklist Types', 'cp' ),
		'not_found'                  => __( 'Checklist Type Not Found', 'cp' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'checklist-type', array( 'checklist' ), $args );

}
add_action( 'init', 'cp_register_checklist_type', 0 );

if( ! term_exists( 'predevelopment', 'checklist-type' ) ) {

	wp_insert_term(
		"Predevelopment",
		'checklist-type',
		array(
			'slug' => 'predevelopment',
			'description' => "
			Create Site Page : Create page in dash
			Choose 5 themes : If using theme, decide
			Send Hello Email to Client : Introduce self, define project scope
			Decide Pages : Outline pages that are to be created, and order copy
			Choose Colors : Choose official hex codes for site
			"
		)
	 );
}


if( ! term_exists( 'development', 'checklist-type' ) ) {

	wp_insert_term(
		"Development",
		'checklist-type',
		array(
			'slug' => 'development',
			'description' => "
			Create Development Folder : Create new folder in development/ and shortcut in defaultroot
			Create Local Database : Setup database on <a href='/phpmyadmin' target='_blank'>local PHPMyAdmin</a> with structure 'client_domain_tld'
			Install WordPress : Table prefix: optimize_, use (link phpmyadmin credential)
			Basic Site Info : Set TimeZone, week start on Sunday, title and tagline, permalink structure to %postname%
			Install Theme : If using, install and configure theme
			Initialize Backup : Set up BackWPUp for daily backups
			Authenticate MainWP Plugin : Configure MainWP client and attach to Control Panel
			Configure External Links plugin : Open in new window, disable icons
			Create Client User : Full organization name as username
			Disable Comments : Under discussion option, disable commenting
			Take Inventory : If developing for existing site, take inventory of existing urls (TODO: include reference to this)
			Add Footer Link : Add <a href='/reference/footer-link/' target='_blank'>attribution link</a> to footer
			Build Contact Forms : Using snippets in site file, configure contact forms
			Build Pages : 'Home' + '/sitemap' + pages decided in predevelopment
			Check In with Client : Check in with client
			"
		)
	 );
}


class Checklist {

	public $type;

	function __construct( $post_id ) {



	}

	public function render( $format = 'full' ) {

		switch( $format ) {

			case 'full':

				foreach( $this->items as $ChecklistItem ) $ChecklistItem->render( $format );

			break;

			case 'embedded':

				foreach( $this->items as $ChecklistItem ) $ChecklistItem->render( $format );

			break;
		}
	}
}

class ChecklistItem {

	public $slug;

	public $completed;

	function __construct( $string ) {

		$parts = explode( ' : ', $string );

		$this->slug = slugify( $parts[0] );

		$this->description = $parts[1];


	}

	public function render( $format = 'full' ) {

		switch( $format ) {

			case 'full':

			break;

			case 'embedded':

				?>
				<div class="checklist__item">

					<span class="checklist__item__title"><?php echo $this->title; ?></span>

					<input type="checkbox" <?php echo $this->checked; ?>>

				</div>
				<?php

			break;
		}

	}
}

trait checklists {


	public function render( $post_id ) {

		foreach( $this->Checklists as $Checklist ) {

			$Checklist->render( 'embedded' );

		}

		echo var_dump($post_id,true);

		echo "hey!";

	}
}


 ?>