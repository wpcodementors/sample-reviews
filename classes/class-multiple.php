<?php
/**
 * Sample Reviews for WooCommerce | Multiple reviews class
 */

namespace Wpdsr;
defined( 'ABSPATH' ) || exit;

use Wpdsr\Helper;
use Wpdsr\Options;

class Multiple {

  private $settings = [];

	/**
	 * Constructor
	 */

	public function __construct(){

    add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );

    add_action( 'wp_ajax_wpdsr_add_multiple', array( $this, '_add_multiple' ) );
    add_action( 'wp_ajax_wpdsr_generate_multiple', array( $this, '_generate_multiple' ) );

  }

  /**
	 * Admin menu page actions
	 */

	public function admin_menu()
 	{

    $labels = [
			'parent_slug' => 'sample-reviews',
      'slug' => 'sample-reviews',
			'title' => 'Add multiple reviews',
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

		wp_enqueue_style( 'wpdsr-dashboard', WPDSR_URL .'assets/css/dashboard.css', [], WPDSR_VERSION );
	  wp_enqueue_script( 'wpdsr-dashboard', WPDSR_URL .'assets/js/dashboard.js', ['jquery'], WPDSR_VERSION, true );

	}

	/**
	 * Template
	 */

	public function callback(){

    $this->settings = $this->set_settings();

		include_once WPDSR_DIR .'classes/templates/multiple.php';

	}

  /**
   * Ajax | Generate reviews
   */

  public function _generate_multiple(){

    check_ajax_referer( 'wpdsr_generate', 'wpdsr_nonce' );

    if( empty($_POST['wpdsr']) ){
      return false;
    }

    // sanitize form data

    $post['count'] = sanitize_text_field($_POST['wpdsr']['count']);
    $post['count'] = explode(';', $post['count']);

    if( ! empty($_POST['wpdsr']['category']) ){
      $post['category'] = array_map('sanitize_text_field', $_POST['wpdsr']['category']);
    }

    $post['numberposts'] = intval( sanitize_text_field($_POST['wpdsr']['numberposts']) );

    $post['rating'] = sanitize_text_field($_POST['wpdsr']['rating']);
    $post['rating'] = explode(';', $post['rating']);

    $post['date_from'] = strtotime( sanitize_text_field($_POST['wpdsr']['date_from']) );
    $post['date_to'] = strtotime( sanitize_text_field($_POST['wpdsr']['date_to']) ) + DAY_IN_SECONDS - 1;

    $hours = intval( ( $post['date_to'] - $post['date_from'] ) / HOUR_IN_SECONDS );

    // get products

    $args = array(
      'post_type'      => 'product',
      'post_status'    => 'publish',
      'orderby'        => 'rand',
    );

    if( ! empty($post['numberposts']) ){
      $args['numberposts'] = $post['numberposts'];
    } else {
      $args['posts_per_page'] = -1;
    }

    if( ! empty($post['category'][0]) ){

      $args['tax_query'] = [[
        'taxonomy' => 'product_cat',
        'field'    => 'id',
        'terms'    => $post['category'],
      ]];

    }

    // print_r($args);

    $products = get_posts($args);

    // print_r($products);

    // get options

    $options = Options::get('settings');

    // reviews

    $reviews = [
      5 => preg_split("/\r\n|\n|\r/", $options['reviews-5'], -1, PREG_SPLIT_NO_EMPTY),
      4 => preg_split("/\r\n|\n|\r/", $options['reviews-4'], -1, PREG_SPLIT_NO_EMPTY),
      3 => preg_split("/\r\n|\n|\r/", $options['reviews-3'], -1, PREG_SPLIT_NO_EMPTY),
      2 => preg_split("/\r\n|\n|\r/", $options['reviews-2'], -1, PREG_SPLIT_NO_EMPTY),
      1 => preg_split("/\r\n|\n|\r/", $options['reviews-1'], -1, PREG_SPLIT_NO_EMPTY),
    ];
    // authors

    $authors = $options['authors'];
    $authors = preg_split("/\r\n|\n|\r/", $authors, -1, PREG_SPLIT_NO_EMPTY);

    // add review

    $return = [];

    foreach( $products as $product ){

      $count = wp_rand( intval($post['count'][0]), intval($post['count'][1]) );

      for ($i = 1; $i <= $count; $i++) {

        $rating = wp_rand($post['rating'][0],$post['rating'][1]);

        $review = $reviews[$rating][wp_rand(0, count($reviews[5]) - 1)];
        if( ! $review ){
          $review = esc_html('-- Please add reviews in plugin settings --', 'wpdsr');
        }

        $author = $authors[wp_rand(0, count($authors) - 1)];
        if( ! $author ){
          $author = esc_html('-- Please add authors in plugin settings --', 'wpdsr');
        }

        $email = urlencode($author) .'@sample_review_for_woocommerce.com';

        $date = $post['date_from'] + wp_rand( 0, $hours ) * HOUR_IN_SECONDS;
        // $date = date_i18n( 'Y-m-d H:i:s', $date );
        $date = date_i18n( 'Y-m-d', $date );

        $element = array(
          'product_id'    => $product->ID,
          'product_title' => esc_html( $product->post_title ),
          'product_image' => get_the_post_thumbnail_url( $product->ID ),
          'author'        => esc_html( $author ),
          'review'        => wp_kses_data( $review ),
          'date'          => $date,
          'rating'        => $rating,
      	);

        $return[] = $element;

      }

    }

    wp_send_json_success( $return );

  }

  /**
   * Ajax | Add reviews
   */

  public function _add_multiple(){

    check_ajax_referer( 'wpdsr_add', 'wpdsr_nonce' );

    if( empty($_POST['wpdsr']) ){
      return false;
    }

    $ids = [];

    foreach( $_POST['wpdsr'] as $k => $review ){

      // sanitize form data

      $review = array_map( 'sanitize_text_field', $review );
      $review = array_map( 'esc_html', $review );
      $review = wp_unslash( $review );

      // skip unpublished

      if( empty($review['publish']) ){
        continue;
      }

      // add review

      $email = urlencode( $review['author'] ) .'@sample_review_for_woocommerce.com';

      $date = strtotime( $review['date'] ) + wp_rand( 0, 1439 ) * MINUTE_IN_SECONDS;
      $date = date_i18n( 'Y-m-d H:i:s', $date );

      $args = array(
        'comment_post_ID'      => $review['product_id'],
        'comment_author'       => $review['author'],
        'comment_author_email' => $email,
        'comment_content'      => $review['review'],
        'comment_type'         => 'review',
        'comment_date'         => $date,
        'comment_approved'     => 1,
        'comment_meta'         => array(
          'rating'             => intval( $review['rating'] ),
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

      // REVIEWS ----------

      [
        'group' => '',
        'type' => 'select',
        'id' => 'count',
        'title' => __('Reviews per product', 'wpdsr'),
        'options' => [
          '1;1' => __('1', 'wpdsr'),
          '2;2' => __('2', 'wpdsr'),
          '3;3' => __('3', 'wpdsr'),
          '4;4' => __('4', 'wpdsr'),
          '5;5' => __('5', 'wpdsr'),

          '0;1' => __('Random 0 to 1', 'wpdsr'),
          '0;2' => __('Random 0 to 2', 'wpdsr'),
          '1;3' => __('Random 1 to 3', 'wpdsr'),
          '1;5' => __('Random 1 to 5', 'wpdsr'),
          '2;4' => __('Random 2 to 4', 'wpdsr'),
          '3;5' => __('Random 3 to 5', 'wpdsr'),
        ],
        'std' => '1;1',
      ],

      [
        'group' => '',
        'type' => 'select',
        'id' => 'rating',
        'title' => __('Rating', 'wpdsr'),
        'desc' => __('Select specified or random rating', 'wpdsr'),
        'options' => [
          '5;5' => __('★★★★★', 'wpdsr'),
          '4;4' => __('★★★★☆', 'wpdsr'),
          '3;3' => __('★★★☆☆', 'wpdsr'),
          '2;2' => __('★★☆☆☆', 'wpdsr'),
          '1;1' => __('★☆☆☆☆', 'wpdsr'),
          '4;5' => __('Random 4★ to 5★', 'wpdsr'),
          '3;5' => __('Random 3★ to 5★', 'wpdsr'),
          '2;5' => __('Random 2★ to 5★', 'wpdsr'),
          '1;5' => __('Random 1★ to 5★', 'wpdsr'),
        ],
        'std' => '5;5',
      ],

      [
        'group' => '',
        'type' => 'select',
        'id' => 'category',
        'title' => __('Category', 'wpdsr'),
        'desc' => __('Select one or more categories', 'wpdsr'),
        'sub_desc' => __('To select multiple categories use ⌘ or Ctrl key', 'wpdsr'),
        'options' => Helper::get_product_categories_hierarchical(),
        'std' => '',
        'multiple' => true,
      ],

      [
        'group' => '',
        'type' => 'text',
        'id' => 'numberposts',
        'title' => __('Number of products', 'wpdsr'),
        'desc' => __('Select number of random products to review', 'wpdsr'),
        'sub_desc' => __('Adding reviews to more than 50 products at a time is not recommended. Please limit reviews to specified category if needed.', 'wpdsr'),
        'attr' => [
          'type' => 'number',
          'min' => '0',
        ],
        'std' => '20',
      ],

      [
        'group' => '',
        'type' => 'text',
        'id' => 'date_from',
        'title' => __('Date from', 'wpdsr'),
        'desc' => __('Dates will be randomized within selected range', 'wpdsr'),
        'attr' => [
          'type' => 'date',
        ],
        'std' => date('Y-m-d'),
      ],

      [
        'group' => '',
        'type' => 'text',
        'id' => 'date_to',
        'title' => __('Date to', 'wpdsr'),
        'attr' => [
          'type' => 'date',
        ],
        'std' => date('Y-m-d'),
      ],

    ];

    return $settings;

  }

}
