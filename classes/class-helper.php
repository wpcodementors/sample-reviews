<?php
/**
 * Sample Reviews for WooCommerce | Helper class
 */

namespace Wpdsr;

defined( 'ABSPATH' ) || exit;

class Helper {

  /**
   * GET produst categories hierarchical
   */

  public static function get_product_categories_hierarchical($parent = 0, $indent = '') {

    $args = array(
      'taxonomy'   => 'product_cat',
      'hide_empty' => false,
      'parent'     => $parent,
    );

    $categories = get_terms($args);
    $category_array = array();

    if (is_wp_error($categories)) {
      return array('error' => $categories->get_error_message());
    }

    // Add 'All' option
    if($parent === 0) {
      $category_array[''] = __('-- All --', 'srw');
    }

    foreach ($categories as $category) {

      if($category->slug == 'uncategorized') {
        continue;
      }

      $category_array[$category->term_id] = $indent . $category->name;

      // Recursively add child categories
      if($children_categories = self::get_product_categories_hierarchical($category->term_id, $indent . '-- ')){
        $category_array += $children_categories;
      }

    }

    return $category_array;
  }

}
