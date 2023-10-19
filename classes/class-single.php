<?php
/**
 * Sample Reviews for WooCommerce | Single review class
 */

namespace Wpdsr;

defined( 'ABSPATH' ) || exit;

use Wpdsr\Helper;
use Wpdsr\Options;

class Single {

  private $settings = [];

	/**
	 * Constructor
	 */

	public function __construct(){

    add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );

    add_action( 'wp_ajax_wpdsr_product_search', array( $this, '_product_search' ) );
    add_action( 'wp_ajax_wpdsr_add_single', array( $this, '_add_single' ) );

  }

  /**
	 * Admin menu page actions
	 */

	public function admin_menu()
 	{

    $labels = [
			'parent_slug' => 'sample-reviews',
      'slug' => 'wpdsr-add-review',
			'title' => 'Add single review',
		];

 		$page = add_submenu_page(
 			$labels['parent_slug'],
 			$labels['title'],
 			$labels['title'],
 			'activate_plugins',
 			$labels['slug'],
 			array( $this, 'callback' )
 		);

    // Fires when styles are printed for a specific admin page based on $hook_suffix.
 		add_action('admin_print_styles-'. $page, array( $this, 'enqueue_dashboard' ));
 	}

  /**
	 * Enqueue plugin pages styles
	 */

	public function enqueue_dashboard(){

    wp_enqueue_style( 'wpdsr-select2', WPDSR_URL .'assets/css/select2.min.css', [], WPDSR_VERSION );
		wp_enqueue_style( 'wpdsr-dashboard', WPDSR_URL .'assets/css/dashboard.css', [], WPDSR_VERSION );

    wp_enqueue_script( 'wpdsr-select2', WPDSR_URL .'assets/js/select2.min.js', ['jquery'], WPDSR_VERSION, true );
	  wp_enqueue_script( 'wpdsr-dashboard', WPDSR_URL .'assets/js/dashboard.js', ['jquery'], WPDSR_VERSION, true );

	}

	/**
	 * Template
	 */

	public function callback(){

    $this->settings = $this->set_settings();

		include_once WPDSR_DIR .'classes/templates/single.php';

	}

  /**
   * Ajax | Search for product
   */

  public function _product_search(){

    check_ajax_referer( 'wpdsr_add', 'wpdsr_nonce' );

    if( empty($_POST['search']) ){
			wp_die();
		}

    $search = sanitize_text_field( $_POST['search'] );
    $search = wp_unslash( $search );

		$args = array(
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'posts_per_page' => 30,
			's'              => $search
		);

		$products = get_posts( $args );

    $return = [];

		foreach ( $products as $product ) {
			$return[] = [ 'id' => $product->ID, 'text' => esc_html( $product->post_title ) ];
		}

		wp_send_json( $return );

  }

  /**
   * Ajax | Add reviews
   */

  public function _add_single(){

    check_ajax_referer( 'wpdsr_add', 'wpdsr_nonce' );

    if( empty($_POST['wpdsr']) ){
      return false;
    }

    // sanitize form data

    $post['rating'] = intval( sanitize_text_field($_POST['wpdsr']['rating']) );

    $post['review'] = wp_unslash( esc_html( sanitize_text_field($_POST['wpdsr']['review']) ) );
    $post['author'] = wp_unslash( esc_html( sanitize_text_field($_POST['wpdsr']['author']) ) );

    $email = urlencode($post['author']) .'@sample_review_for_woocommerce.com';

    $post['date'] = strtotime( sanitize_text_field($_POST['wpdsr']['date']) ) ?? current_time( 'U' );
    $post['date'] = date_i18n( 'Y-m-d H:i:s', $post['date'] );

    $post['product_id'] = array_map( 'sanitize_text_field', $_POST['wpdsr']['product_id'] );

    // prepare data

    if( ! is_array($post['product_id']) ){
      wp_send_json_error();
    }

    foreach( $post['product_id'] as $product_id ){

      $args = array(
        'comment_post_ID'      => intval( $product_id ),
        'comment_author'       => $post['author'],
        'comment_author_email' => $email,
        'comment_content'      => $post['review'],
        'comment_type'         => 'review',
        'comment_date'         => $post['date'],
        'comment_approved'     => 1,
        'comment_meta'         => array(
          'rating'             => $post['rating'],
          'wpdsr'              => 1
        )
      );

      // print_r($args);

      $ids[] = wp_insert_comment( $args );

      \WC_Comments::clear_transients( $review['product_id'] );

    }

    wp_send_json_success( $ids );

  }

  /**
   * Set settings
   */

  public function set_settings(){

    $settings = [

      [
        'group' => '',
        'type' => 'select',
        'id' => 'rating',
        'title' => __('Rating', 'wpdsr'),
        'desc' => __('Select product rating', 'wpdsr'),
        'options' => [
          '5' => __('★★★★★', 'wpdsr'),
          '4' => __('★★★★☆', 'wpdsr'),
          '3' => __('★★★☆☆', 'wpdsr'),
          '2' => __('★★☆☆☆', 'wpdsr'),
          '1' => __('★☆☆☆☆', 'wpdsr'),
        ],
        'std' => '5',
      ],

      [
        'group' => '',
        'type' => 'textarea',
        'id' => 'review',
        'title' => __('Review', 'wpdsr'),
        'desc' => __('Review content in plain text', 'wpdsr'),
        'std' => '',
      ],

      [
        'group' => '',
        'type' => 'text',
        'id' => 'author',
        'title' => __('Author', 'wpdsr'),
        'desc' => __('Review author', 'wpdsr'),
        'std' => '',
      ],

      [
        'group' => '',
        'type' => 'text',
        'id' => 'date',
        'title' => __('Date', 'wpdsr'),
        'desc' => __('Date and time of review', 'wpdsr'),
        'attr' => [
          'type' => 'datetime-local',
        ],
        'std' => date('Y-m-d H:m'),
      ],

      [
        'group' => '',
        'type' => 'select',
        'id' => 'product_id',
        'title' => __('Product', 'wpdsr'),
        'desc' => __('Select single product or multiple products', 'wpdsr'),
        'class' => 'wpdsr-field-search-product',
        'options' => [],
        'multiple' => true,
        'std' => '',
      ],

    ];

    return $settings;

  }

}
