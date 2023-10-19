<?php

namespace Wpdsr;
defined( 'ABSPATH' ) || exit;

class FieldSelect {

  public function __construct( $field, $value = '' ){

    // name

    $name = 'wpdsr['. esc_attr($field['id']) .']';

    // multiselect

    if( !empty($field['multiple']) ){
      $multiple = 'multiple';
      $name .= '[]';
    } else {
      $multiple = '';
    }

    // output -----

    echo '<select class="wpdsr-form-control wpdsr-form-select" name="'. esc_attr($name) .'" id="wpdsr['. esc_attr($field['id']) .']" '. esc_attr($multiple) .'>';

      foreach( $field['options'] as $key => $val ){
        echo '<option value="'. esc_attr($key) .'" '. selected($value, $key, false) .'>'. esc_attr($val) .'</option>';
      }

    echo '</select>';

    if( ! empty($field['sub_desc']) ){
      echo '<p>'. esc_html($field['sub_desc']) .'</p>';
    }

  }

}
