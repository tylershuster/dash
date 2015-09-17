<?php

class GenericCPObject {

	public $id;

	public $db;

	public $cards;

	public static $login_types = array(
		'ftp' => 'FTP Login',
		'hosting' => 'Hosting Login',
		'domain' => 'Domain Login',
		'cms' => 'CMS Login',
		'database' => 'Database Login',
		'service' => 'Web Service Login',
		'social' => 'Social Network Login'
	);

	function __construct( $post_id ) {

		$this->id = $post_id;

		foreach( $this->cards as $card_slug => $card_args ) {

			$this->Cards[$card_slug] = new Card( $card_slug, $card_args, $this->id );

		}

	}

	public static function select( $type, $return_type = 'id' ) {

		$return = array();

		$args = array(
			'post_type'   => $type,
		);

		$query = new WP_Query( $args );

		if( $query->have_posts() ) {

			while( $query->have_posts() ) {

				$query->the_post();

				switch( $return_type ) {

					case 'id':

						$return[get_the_id()] = get_the_title();

					break;

					case 'url':

						$return[get_the_permalink()] = get_the_title();

					break;

				}

			}

		}

		wp_reset_postdata();

		return $return;

	}

	public function save() {}



}

class Card {

	public $slug;

	protected $renderfunc;

	protected $data = array();

	public function __construct( $slug, $args, $parent_id ) {

		$this->slug = $slug;

		$this->name = isset( $args['name'] ) ? $args['name'] : unslugify( $slug );

		$this->parent_id = $parent_id ? $parent_id : false;

		if( $args['fields'] ) {

			foreach( $args['fields'] as $field_slug => $field_args ) {

				$this->Fields[$field_slug] = new Field( $field_slug, $field_args, $this->parent_id );

			}

			$meta_prefix = "_dash_{$slug}_";

			$postmeta = get_post_meta( $this->parent_id );

			foreach( $postmeta as $meta_key => $meta_value ) {

				if( strpos( $meta_key, $meta_prefix ) !== false ) {

					$key = str_replace( $meta_prefix, '', $meta_key );

					$path = explode( '_', $key );

					$value = dash_array( $path, $meta_value[0] );

					$key = $path[0];


					if( isset($this->data[$key]) ) {

						$this->data[$key] = self::merge_data( $this->data[$key], $value );

					} else {

						$this->data[$key] = $value;

					}

				}

			}

			foreach( $this->data as $field => $data ) {

				$this->Fields[$field]->setData($data);

			}

		}

		if( isset( $args['renderfunc'] ) ) {

			$this->renderfunc = $args['renderfunc'];

		}


	}

	public static function merge_data( $old_array, $new_array ) {

		//DEBUG: echo "old:" . print_r($old_array, true) . PHP_EOL . "new: " . print_r($new_array,true);

		$return = array();

		foreach( $new_array as $new_key => $new_val ) {

			if( $old_array[$new_key] ) {

				$old_array[$new_key] = self::merge_data( $old_array[$new_key], $new_val );

			} else {

				$old_array[$new_key] = $new_val;

			}

		}

		return $old_array;


	}

	public function render() {

		$return = '';

		ob_start(); ?>

			<div id="<?php echo $this->slug; ?>" class='card'>

				<h2><?php echo $this->name; ?></h2>

				<ul class='fields'>

				<?php

				if( $this->Fields ) {

					foreach( $this->Fields as $Field ){

						echo "<li>" . $Field->render() . "</li>";

					}

				} elseif( $this->renderfunc ) {

					call_user_func( $this->renderfunc[0] . "::" . $this->renderfunc[1], $this->parent_id );

				}

				?>

				</ul>

			</div>

		<?php $return = ob_get_clean();

		return $return;

	}

}

class FieldFactory {

	public static function schema( $type, $label, $args = false ) {

		switch( $type ) {

			case 'credential':

				return array(
					'username' => FieldFactory::schema('text', 'Username'),
					'password' => FieldFactory::schema('text', 'Password'),
					'notes' => FieldFactory::schema('textarea', 'Notes'),
				);

			break;

			default:

				return array(
					'type' => $type,
					'name' => $label
				);

			break;
		}
	}
}

class Field {

	public $type;

	public $name;

	public $options;

	protected $value;

	public $slug;

	public $placeholder;

	public $parent_id;

	public $add_label;

	function __construct( $slug, $args, $parent_id = false ) {

		$this->slug = $slug;

		$this->type = $args['type'];

		$this->options = $args['options'];

		$this->parent_id = $parent_id;

		$this->add_label = isset($args['add-label']) ? $args['add-label'] : "Add" . unslugify( $slug );

		$this->placeholder = isset($args['name']) ? $args['name'] : null;

		$this->value = '';

	}

	public function setData( $data ) {

		$this->value = $data;

	}

	public function render() {

		ob_start();

		switch( $this->type ) {

			case 'text':

				echo "<div class='dash-data'>";

				echo $this->placeholder ? "<label for='{$this->slug}'>{$this->placeholder}</label>" : '';

				echo "<input type='text' name='{$this->slug}' placeholder='{$this->placeholder}' value='{$this->value}' />";

				echo "</div>";

			break;

			case 'color':

				echo "<div class='dash-data'>";

				echo $this->placeholder ? "<label for='{$this->slug}'>{$this->placeholder}</label>" : '';

				echo "<input type='color' name='{$this->slug}' placeholder='{$this->placeholder}' value='{$this->value}' />";

				echo "</div>";

			break;

			case 'textarea':

				echo "<div class='dash-data'>";

				echo $this->placeholder ? "<label for='{$this->slug}'>{$this->placeholder}</label>" : '';

				echo "<textarea name='{$this->slug}'>{$this->value}</textarea>";

				echo "</div>";

			break;

			case 'select':

				echo "<div class='dash-data'>";

				if( is_array($this->options) ) {

					echo $this->placeholder ? "<label for='{$this->slug}'>{$this->placeholder}</label>" : '';

					echo "<select name='{$this->slug}'>";

					foreach( $this->options as $slug => $value ) {

						$selected = $this->value == $slug ? 'selected="selected"' : '';

						echo "<option value='$slug' $selected>$value</option>";

					}

				}


				echo "</select>";

				echo "</div>";

			break;

			case 'tel':

				echo "<div class='dash-data'>";

				echo $this->placeholder ? "<label for='{$this->slug}'>{$this->placeholder}</label>" : '';

				echo "<input type='tel' name='{$this->slug}' placeholder='{$this->placeholder}' value='{$this->value}' />";

				echo "</div>";

			break;

			case 'email':

				echo "<div class='dash-data'>";

				echo $this->placeholder ? "<label for='{$this->slug}'>{$this->placeholder}</label>" : '';

				echo "<input type='email' name='{$this->slug}' placeholder='{$this->placeholder}' value='{$this->value}' />";

				echo "</div>";

			break;

			case 'password':

				echo "<div class='dash-data'>";

				echo $this->placeholder ? "<label for='{$this->slug}'>{$this->placeholder}</label>" : '';

				echo "<input type='password' name='{$this->slug}' placeholder='{$this->placeholder}' value='{$this->value}' />";

				echo "</div>";

			break;

			case 'url':

				echo "<div class='dash-data'>";

				echo $this->placeholder ? "<label for='{$this->slug}'>{$this->placeholder}</label>" : '';

				echo "<input type='url' name='{$this->slug}' placeholder='{$this->placeholder}' value='{$this->value}' />";

				echo "</div>";

			break;

			case 'checkbox':break;

			case 'array':

				echo "<ul class='array' name='" . $this->slug . "'>";

					echo "<li class='new-array-item'>";

					foreach( $this->options as $item_slug => $item_args ){

						$Item = new Field( $item_slug, $item_args, $parent_id );

						 echo $Item->render();

					}

					echo "</li>";

					if( is_array( $this->value ) ) {

						foreach( $this->value as $i => $value ) {

							echo "<li class='" . $this->slug . "'>";

							foreach( $this->options as $item_slug => $item_args ) {

								$Item = new Field( $item_slug	, $item_args, $parent_id );

								$Item->setData($this->value[$i][$item_slug]);

								echo $Item->render();

							}

							echo "</li>";

						}

					}

				echo "</ul>";

				echo "<button class='new-array-item'>{$this->add_label}</button>";

			break;

		}

		return ob_get_clean();

	}

}


 ?>