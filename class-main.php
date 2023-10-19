<?php
/**
 * Sample Reviews for WooCommerce | Main class
 */

namespace Wpdsr;

defined( 'ABSPATH' ) || exit;

use Wpdsr\Options;

class Main {

	/**
	 * Constructor
	 */

	public function __construct(){

		$this->set_options();

		// actions

		add_action( 'plugins_loaded', [ $this, 'include_classes' ], 20 );
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 20 );

		add_filter( 'plugin_action_links_' . WPDSR_FILE, [ $this, 'action_links' ] );

		add_filter( 'woocommerce_product_reviews_list_table_item_types', [ $this, 'item_types' ] );
		add_filter( 'woocommerce_product_reviews_list_table_prepare_items_args', [ $this, 'prepare_items_args' ] );

	}

	/**
	 * SET options
	 */

	private function set_options(){

		Options::set( 'settings', get_option( 'wpdsr_settings', [] ) );

	}

	/**
	 * Admin menu page actions
	 */

	public function admin_menu()
 	{

		$labels = [
			'slug' => 'sample-reviews',
			'title' => 'Sample Reviews',
		];

 		$page = add_menu_page(
 			$labels['title'],
 			$labels['title'],
 			'activate_plugins',
 			$labels['slug'],
 			null,
 			'dashicons-star-empty',
 			56
 		);
 	}

	/**
	 * Include classes
	 */

	public function include_classes(){

		$classes = [
			'Wpdsr\\Multiple' => WPDSR_DIR . 'classes/class-multiple.php',
			'Wpdsr\\Single' => WPDSR_DIR . 'classes/class-single.php',
			'Wpdsr\\Dashboard' => WPDSR_DIR . 'classes/class-dashboard.php',
		];

		foreach ($classes as $class => $file ) {
			require_once $file;
			$obj = new $class();
		}

	}

	/**
	 * GET fields
	 */

	public static function get_fields(){

 		$fields = [
			'text'	=> [ 'Wpdsr\\FieldText', WPDSR_DIR . 'classes/fields/class-field-text.php' ],
			'select'	=> [ 'Wpdsr\\FieldSelect', WPDSR_DIR . 'classes/fields/class-field-select.php' ],
			'textarea'	=> [ 'Wpdsr\\FieldTextarea', WPDSR_DIR . 'classes/fields/class-field-textarea.php' ],
 		];

		return $fields;

 	}

	/**
	 * Plugins page 'settings' link
	 */

	public function action_links( $links ) {

 		$settings = array( '<a href="'. admin_url('admin.php?page=wpdsr-settings') .'">'. esc_html__( 'Settings', 'wpdsr' ) .'</a>' );

 		return array_merge( $links, $settings );
 	}

	/**
	 * Filter reviews list
	 */

	public function item_types( $type ) {
		$type['wpdsr'] = esc_html__( 'Sample reviews', 'wpdsr' );

		return $type;
	}

	public function prepare_items_args( $args ) {
		if ( ! empty( $args['type'] ) && $args['type'] == 'wpdsr' ) {
			$args['type']       = 'review';
			$args['meta_key']   = 'wpdsr';
			$args['meta_value'] = 1;
		}

		return $args;
	}

}
