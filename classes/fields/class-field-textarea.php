<?php

namespace Wpdsr;
defined( 'ABSPATH' ) || exit;

class FieldTextarea {

  public function __construct( $field, $value = '' ){

    echo '<textarea class="wpdsr-form-control wpdsr-form-textarea" name="wpdsr['. esc_attr($field['id']) .']" id="wpdsr['. esc_attr($field['id']) .']">'. esc_html($value) .'</textarea>';

    if( ! empty($field['sub_desc']) ){
      echo '<p>'. esc_html($field['sub_desc']) .'</p>';
    }

  }

}
