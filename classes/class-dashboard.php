<?php
/**
 * Sample Reviews for WooCommerce | Dashboard class
 */

namespace Wpdsr;

defined( 'ABSPATH' ) || exit;

use Wpdsr\Options;

class Dashboard {

  private $options = [];
  private $settings = [];

	/**
	 * Constructor
	 */

	public function __construct(){

    $this->options = Options::get('settings');

    if( ! $this->options ){
      $this->options = $this->set_options_first();
    }

    add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );

    add_action( 'wp_ajax_wpdsr_save_settings', array( $this, '_save_dashboard' ) );

  }

  /**
	 * Admin menu page actions
	 */

	public function admin_menu()
 	{

    $labels = [
			'parent_slug' => 'sample-reviews',
      'slug' => 'wpdsr-settings',
			'title' => 'Settings',
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
	  wp_enqueue_script( 'wpdsr-dashboard', WPDSR_URL .'assets/js/dashboard.js', ['jquery','wp-color-picker'], WPDSR_VERSION, true );

	}

	/**
	 * Callback
	 */

	public function callback(){

		$this->settings = $this->set_settings();

    // template

    $this->template();

	}

	/**
	 * Template
	 */

	public function template(){

		include_once WPDSR_DIR .'classes/templates/dashboard.php';

	}

  /**
   * Ajax | Settings save
   */

  public function _save_dashboard(){

    check_ajax_referer( 'wpdsr_settings', 'wpdsr_nonce' );

    if( empty($_POST['wpdsr']) ){
      return false;
    }

    // sanitize form data

    $options = array_map( 'sanitize_textarea_field', wp_unslash( $_POST['wpdsr'] ) );

    update_option( 'wpdsr_settings', $options );

    exit();

  }

  /**
   * Set options if do not exist
   */

  public function set_options_first(){

    $options = [];
    $settings = $this->set_settings();

    foreach( $settings as $setting ){
      $options[$setting['id']] = $setting['std'];
    }

    // set plugin settings in Options class

    Options::set( 'settings', $options );

    // update database option

    update_option( 'wpdsr_settings', $options );

    return $options;

  }

  /**
   * Set settings
   */

  public function set_settings(){

    $settings = [

      // REVIEWS ----------

      [
        'group' => 'reviews',
        'type' => 'textarea',
        'id' => 'reviews-5',
        'title' => __('5★ reviews', 'wpdsr'),
        'desc' => __('Reviews for 5 star ratings', 'wpdsr'),
        'sub_desc' => __('Enter each review in new line.', 'wpdsr'),
        'std' => 'Impressive product! It delivers on its promises and has made a positive impact on my daily routine. Very satisfied with the purchase.
Top-notch! This product has exceeded my expectations. It\'s reliable and easy to use, making it a valuable addition to my belongings.
Highly recommended! I\'m thoroughly impressed with the quality and performance of this product. It\'s been a game-changer for me.
Five stars all the way! This product is worth every penny. It\'s user-friendly and has improved my productivity significantly.
Outstanding! I can\'t fault this product in any way. It does exactly what it\'s supposed to do, and I\'m extremely happy with it.
Excellent purchase! The product lives up to the hype, and I couldn\'t be happier with the results. Definitely worth considering.
Fantastic product! It\'s well-designed and functions perfectly. I\'ve had a great experience using it so far.
A must-have! This product has made a positive difference in my daily life. I highly recommend giving it a try.
Satisfied customer! I\'ve had no regrets purchasing this product. It\'s dependable and has become an essential part of my routine.
No complaints at all! This product is everything I hoped for and more. It\'s made my tasks easier and more enjoyable.'
      ],

      [
        'group' => 'reviews',
        'type' => 'textarea',
        'id' => 'reviews-4',
        'title' => __('4★ reviews', 'wpdsr'),
        'desc' => __('Reviews for 4 star ratings', 'wpdsr'),
        'sub_desc' => __('Enter each review in new line.', 'wpdsr'),
        'std' => 'Great product! It delivers on its promises and has made a positive impact on my daily routine. Very satisfied with the purchase.
Solid performance! This product has exceeded my expectations. It\'s reliable and easy to use, making it a valuable addition to my belongings.
Highly recommended! I\'m impressed with the quality and performance of this product. It\'s been a useful addition to my life.
Very good! This product is worth the investment. It\'s user-friendly and has improved my productivity significantly.
Impressive results! I can\'t fault this product in any major way. It does its job effectively, and I\'m happy with it.
Almost perfect! The product lives up to its claims, and I\'m quite satisfied with the results. Definitely worth considering.
Good value! This product functions well and has been a positive addition to my daily routine.
Almost there! This product has made a difference in my life. I recommend it, but there\'s room for minor improvements.
Reliable choice! I\'ve had a good experience with this product. It\'s dependable and serves its purpose well.
Happy with it! This product has fulfilled most of my expectations. It\'s effective and has made certain tasks easier for me.',
      ],

      [
        'group' => 'reviews',
        'type' => 'textarea',
        'id' => 'reviews-3',
        'title' => __('3★ reviews', 'wpdsr'),
        'desc' => __('Reviews for 3 star ratings', 'wpdsr'),
        'sub_desc' => __('Enter each review in new line.', 'wpdsr'),
        'std' => 'Decent product! It has some useful features and serves its purpose reasonably well. It\'s an okay purchase overall.
Has potential! This product has a few good aspects, but there\'s room for improvement. It\'s been helpful in certain situations.
Not bad! The product has its pros and cons, but I\'ve managed to find some value in it. It\'s an average buy.
Adequate performance! While it falls short in some areas, this product has been somewhat useful to me.
Shows promise! This product has some positive aspects, but it didn\'t fully meet my expectations.
Okay for the price! It\'s not the best, but considering the cost, this product offers decent functionality.
Mixed feelings! This product has both good and not-so-good aspects. It\'s been a somewhat satisfactory purchase.
Average results! I had hoped for a bit more, but this product has some merit.
Functional but lacking! This product serves its purpose to some extent, but it could be better.
Fair performance! While it has its drawbacks, this product has been of some use to me.',
      ],

      [
        'group' => 'reviews',
        'type' => 'textarea',
        'id' => 'reviews-2',
        'title' => __('2★ reviews', 'wpdsr'),
        'desc' => __('Reviews for 2 star ratings', 'wpdsr'),
        'sub_desc' => __('Enter each review in new line.', 'wpdsr'),
        'std' => 'Not the best experience! This product has some issues, but I\'ve managed to find limited usefulness in it.
Has a few drawbacks! While it has some redeeming qualities, this product falls short in several aspects.
Underwhelming performance! I expected more from this product, but it has provided only marginal benefits.
Some room for improvement! This product has disappointed me in some ways, but it\'s not entirely without merit.
Mediocre at best! I\'ve encountered various problems with this product, but I\'ve still found a few minor uses for it.
Needs significant enhancements! This product has not lived up to my expectations, but I\'m trying to make the most of it.
Not impressed! While this product has its moments, it has not delivered the results I hoped for.
Limited usefulness! This product falls short in many areas, but I have found a couple of applications for it.
Not very reliable! I\'ve faced some challenges with this product, but it has had a few small positive moments.
Disappointing performance! This product has struggled to meet my needs, but I\'ve managed to extract a tiny bit of value from it.',
      ],

      [
        'group' => 'reviews',
        'type' => 'textarea',
        'id' => 'reviews-1',
        'title' => __('1★ reviews', 'wpdsr'),
        'desc' => __('Reviews for 1 star ratings', 'wpdsr'),
        'sub_desc' => __('Enter each review in new line.', 'wpdsr'),
        'std' => 'Not recommended! This product has been a major disappointment. I haven\'t found any significant value in it.
Poor performance! Unfortunately, this product did not meet even the basic requirements I had in mind.
Very unsatisfactory! I regret purchasing this product as it has been mostly ineffective.
Fails to deliver! This product falls far below my expectations, and I cannot find any positive aspects.
Avoid if possible! I had a terrible experience with this product and would not recommend it to anyone.
Severely lacking! This product has been nothing short of a letdown, and I cannot find any practical use for it.
Waste of money! I wish I had chosen a different product as this one has been a complete failure.
Non-functional! I have faced numerous issues with this product, and it has not served its intended purpose.
Highly disappointed! I cannot find anything positive to say about this product, and I regret my purchase.
Useless! This product has been a total waste of time and money, and I would not recommend it to anyone.',
      ],

      // AUTHORS ----------

      [
        'group' => 'authors',
        'type' => 'textarea',
        'id' => 'authors',
        'title' => __('Authors', 'wpdsr'),
        'desc' => __('Authors of reviews', 'wpdsr'),
        'sub_desc' => __('Enter each author name in new line.', 'wpdsr'),
        'std' => 'Abigail
Amelia
Andrew
Ava
Benjamin
Charlotte
Christopher
Daniel
David
Emily
Emma
Harper
Isabella
James
Jessica
John
Joseph
Mia
Michael
Olivia
Robert
Samuel
Sophia
William',
      ],

    ];

    return $settings;

  }

}
