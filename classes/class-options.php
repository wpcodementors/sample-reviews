<?php
/**
 * Sample Reviews for WooCommerce | Options class
 */

namespace Wpdsr;

defined( 'ABSPATH' ) || exit;

class Options {

	private static $options = [];

  /**
	 * GET options
	 */

  public static function get( $key ) {

    if( isset( self::$options[$key] ) ){
      return self::$options[$key];
    }

 		return false;

 	}

	/**
	 * SET options
	 */

	public static function set( $key, $value = '' ){

    if ( is_array($key) ) {
			foreach ( $key as $k => $v ) {
				self::$options[$k] = $v;
			}
		} else {
			self::$options[$key] = $value;
		}

	}

}
